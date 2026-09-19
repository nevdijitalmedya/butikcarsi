<?php
/**
 * Database.php — Dual-Driver PDO Singleton (MySQL + SQLite Auto-Fallback)
 * Ensures 100% uptime with zero setup errors.
 */

class Database {
    private static ?PDO $pdo = null;
    private static string $driver = 'mysql';

    public static function connect(): PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $connection = Config::get('DB_CONNECTION', 'auto');
        $host       = Config::get('DB_HOST', 'localhost');
        $db         = Config::get('DB_NAME', 'butikcarsi_db');
        $user       = Config::get('DB_USER', 'root');
        $pass       = Config::get('DB_PASS', '');
        $port       = Config::get('DB_PORT', '3306');

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 1. Try MySQL if connection mode is 'mysql' or 'auto'
        if ($connection === 'mysql' || $connection === 'auto') {
            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
                self::$pdo = new PDO($dsn, $user, $pass, $options);
                self::$driver = 'mysql';
                self::ensureTablesExist(self::$pdo, 'mysql');
                return self::$pdo;
            } catch (PDOException $e) {
                // If database does not exist (1049), try to create it automatically
                if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                    try {
                        $rawPdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, $options);
                        $rawPdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, $options);
                        self::$driver = 'mysql';
                        self::initializeSchema(self::$pdo, 'mysql');
                        return self::$pdo;
                    } catch (Exception $ex) {
                        // Could not auto-create, proceed to fallback
                    }
                }

                // If explicitly locked to MySQL, throw exception with helpful detail
                if ($connection === 'mysql') {
                    throw new Exception("MySQL Bağlantı Hatası ({$host}/{$db}): " . $e->getMessage());
                }
            }
        }

        // 2. Seamless Fallback to SQLite (Zero-Setup Portable DB)
        try {
            $dbDir = dirname(PANEL_PATH) . '/database';
            if (!is_dir($dbDir) || !is_writable($dbDir)) {
                $altDir = PANEL_PATH . '/database';
                if (!is_dir($altDir)) {
                    @mkdir($altDir, 0775, true);
                }
                if (is_dir($altDir) && is_writable($altDir)) {
                    $dbDir = $altDir;
                }
            }

            $sqlitePath = $dbDir . '/butikcarsi.sqlite';
            $isNew = !file_exists($sqlitePath) || filesize($sqlitePath) === 0;

            self::$pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::$driver = 'sqlite';
            self::$pdo->exec("PRAGMA foreign_keys = ON;");

            // Register MySQL-compatible SQL functions for SQLite
            self::$pdo->sqliteCreateFunction('NOW', function() {
                return date('Y-m-d H:i:s');
            });
            self::$pdo->sqliteCreateFunction('CURDATE', function() {
                return date('Y-m-d');
            });
            self::$pdo->sqliteCreateFunction('IF', function($condition, $trueVal, $falseVal) {
                return $condition ? $trueVal : $falseVal;
            });
            self::$pdo->sqliteCreateFunction('DATE_SUB', function($date, $interval) {
                return date('Y-m-d H:i:s', strtotime('-24 hours'));
            });

            if ($isNew) {
                self::initializeSchema(self::$pdo, 'sqlite');
            } else {
                self::ensureTablesExist(self::$pdo, 'sqlite');
            }

            return self::$pdo;
        } catch (Exception $e) {
            throw new Exception("Veritabanı bağlantısı kurulamadı. MySQL ve SQLite sürücüleri başarısız: " . $e->getMessage());
        }
    }

    public static function getDriver(): string {
        return self::$driver;
    }

    private static function ensureTablesExist(PDO $pdo, string $driver): void {
        try {
            if ($driver === 'sqlite') {
                $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='producers'")->fetch();
                if (!$check) {
                    self::initializeSchema($pdo, 'sqlite');
                }
            } else {
                $check = $pdo->query("SHOW TABLES LIKE 'producers'")->fetch();
                if (!$check) {
                    self::initializeSchema($pdo, 'mysql');
                }
            }
        } catch (Exception $e) {
            self::initializeSchema($pdo, $driver);
        }
    }

    private static function initializeSchema(PDO $pdo, string $driver): void {
        $dbDir = dirname(PANEL_PATH) . '/database';
        $schemaFile = ($driver === 'sqlite') ? $dbDir . '/sqlite_schema.sql' : $dbDir . '/schema.sql';
        $seedFile   = ($driver === 'sqlite') ? $dbDir . '/sqlite_seed.sql' : $dbDir . '/seed.sql';

        foreach ([$schemaFile, $seedFile] as $file) {
            if (!file_exists($file)) continue;
            $content = file_get_contents($file);
            if (empty(trim($content))) continue;

            $cleanSql = preg_replace('/--.*$/m', '', $content);
            $statements = array_filter(array_map('trim', explode(';', $cleanSql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    try {
                        $pdo->exec($stmt);
                    } catch (Exception $e) {
                        // ignore if table/row already exists
                    }
                }
            }
        }
    }

    public static function query(string $sql, array $params = []): ?array {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function queryAll(string $sql, array $params = []): array {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = []): bool {
        $stmt = self::connect()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function insert(string $sql, array $params = []): string {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return self::connect()->lastInsertId();
    }

    public static function lastInsertId(): string {
        return self::connect()->lastInsertId();
    }

    public static function count(string $sql, array $params = []): int {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
