<?php
/**
 * config.php â€” Central Application Bootstrap and Autoloader
 * ButikÃ‡arÅŸÄ± Marketplace Platform
 */

if (!defined('PANEL_PATH')) {
    define('PANEL_PATH', __DIR__);
}

// Autoload Composer vendor classes if present
$vendorAutoload = PANEL_PATH . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// Autoload Core Classes
spl_autoload_register(function ($class) {
    $file = PANEL_PATH . '/core/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize Config Loader
Config::init();

// Initialize Database connection
try {
    Database::connect();
} catch (Exception $e) {
    if (Config::get('APP_DEBUG', false)) {
        die("Database connection failed: " . $e->getMessage());
    }
    die("Database connection failed. Check your database setup and .env file.");
}

// Start Session safely
Auth::initSession();
