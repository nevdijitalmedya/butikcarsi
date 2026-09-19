<?php
/**
 * Config.php — Environment variable loader (.env) and platform settings accessor
 */

class Config {
    private static array $env = [];
    private static array $settings = [];
    private static bool $settingsLoaded = false;

    /**
     * Load .env file
     */
    public static function init(): void {
        $envFile = PANEL_PATH . '/.env';
        if (!file_exists($envFile) && file_exists(PANEL_PATH . '/.env.example')) {
            @copy(PANEL_PATH . '/.env.example', $envFile);
        }
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    self::$env[trim($key)] = trim($value);
                }
            }
        }
    }

    /**
     * Get environment variable
     */
    public static function get(string $key, mixed $default = null): mixed {
        return self::$env[$key] ?? $_ENV[$key] ?? $default;
    }

    /**
     * Get platform setting from database
     */
    public static function setting(string $key, mixed $default = null): mixed {
        if (!self::$settingsLoaded) {
            self::loadSettings();
        }
        return self::$settings[$key] ?? $default;
    }

    /**
     * Load all platform settings from DB
     */
    private static function loadSettings(): void {
        try {
            $rows = Database::queryAll("SELECT setting_key, setting_value FROM platform_settings");
            foreach ($rows as $row) {
                self::$settings[$row['setting_key']] = $row['setting_value'];
            }
            self::$settingsLoaded = true;
        } catch (Exception $e) {
            self::$settingsLoaded = true; // Prevent infinite retry
        }
    }

    /**
     * Update a platform setting
     */
    public static function updateSetting(string $key, string $value): bool {
        if (Database::getDriver() === 'sqlite') {
            $existing = Database::query("SELECT id FROM platform_settings WHERE setting_key = ?", [$key]);
            if ($existing) {
                $result = Database::execute("UPDATE platform_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
            } else {
                $result = Database::execute("INSERT INTO platform_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
            }
        } else {
            $result = Database::execute(
                "INSERT INTO platform_settings (setting_key, setting_value) 
                 VALUES (?, ?) 
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
                [$key, $value]
            );
        }
        if ($result) {
            self::$settings[$key] = $value;
        }
        return $result;
    }
}
