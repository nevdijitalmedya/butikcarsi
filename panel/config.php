<?php
/**
 * config.php — Central Application Bootstrap and Autoloader
 * ButikÇarşı Marketplace Platform
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

// Initialize Database connection safely
try {
    Database::connect();
} catch (Exception $e) {
    $dbException = $e;
    require_once PANEL_PATH . '/setup-notice.php';
    exit;
}

// Start Session safely
Auth::initSession();
