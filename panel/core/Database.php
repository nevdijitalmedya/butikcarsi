<?php
/**
 * Database.php â€” PDO Singleton Wrapper for secure MySQL access
 */

class Database {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
        if (self::$pdo === null) {
            $host = Config::get('DB_HOST', 'localhost');
            $db   = Config::get('DB_NAME', 'butikcarsi_db');
            $user = Config::get('DB_USER', 'root');
            $pass = Config::get('DB_PASS', '');
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection error");
            }
        }
        return self::$pdo;
    }

    /**
     * Query single row
     */
    public static function query(string $sql, array $params = []): ?array {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Query all rows
     */
    public static function queryAll(string $sql, array $params = []): array {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute INSERT, UPDATE, DELETE
     */
    public static function execute(string $sql, array $params = []): bool {
        $stmt = self::connect()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get last inserted ID
     */
    public static function lastInsertId(): string {
        return self::connect()->lastInsertId();
    }

    /**
     * Count query helper
     */
    public static function count(string $sql, array $params = []): int {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function beginTransaction(): bool {
        return self::connect()->beginTransaction();
    }

    public static function commit(): bool {
        return self::connect()->commit();
    }

    public static function rollBack(): bool {
        return self::connect()->rollBack();
    }
}
