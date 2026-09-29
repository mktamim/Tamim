<?php
/**
 * Bootstrap File
 * Portfolio Website - Includes
 */

// Define base path
define('BASE_PATH', __DIR__ . '/..');

// Load Composer autoloader if exists
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
}

// Load configuration
require BASE_PATH . '/config/app.php';

// Load core functions
require BASE_PATH . '/includes/functions.php';

// Auto-fix site_url if localhost or empty (runs on every page load in production)
$siteUrl = setting('site_url');
if (!$siteUrl || strpos($siteUrl, 'localhost') !== false || strpos($siteUrl, '127.0.0.1') !== false || strpos($siteUrl, '/Tamim') !== false) {
    $detectedUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    // Use INSERT ... ON DUPLICATE KEY UPDATE (upsert) since setting_key is UNIQUE
    db_execute('INSERT INTO settings (setting_key, setting_value, setting_type, group_name, label, sort_order) VALUES ("site_url", ?, "text", "general", "Site URL", 0) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()', [$detectedUrl]);
}

// Set timezone
date_default_timezone_set(config('app.timezone'));

// Error handling
if (config('app.debug')) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
}

// Custom error handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Custom exception handler
set_exception_handler(function ($exception) {
    if (config('app.debug')) {
        echo '<h1>Error</h1>';
        echo '<p><strong>Message:</strong> ' . e($exception->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . e($exception->getFile()) . ':' . $exception->getLine() . '</p>';
        echo '<pre>' . e($exception->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
});

// Handle fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (config('app.debug')) {
            echo '<h1>Fatal Error</h1>';
            echo '<p><strong>Message:</strong> ' . e($error['message']) . '</p>';
            echo '<p><strong>File:</strong> ' . e($error['file']) . ':' . $error['line'] . '</p>';
        } else {
            http_response_code(500);
            echo '<h1>500 - Internal Server Error</h1>';
        }
    }
});