<?php
/**
 * Application Configuration
 * Portfolio Website - Config
 */

// Load environment-specific settings
$env = parse_ini_file(__DIR__ . '/../.env', true) ?: [];

return [
    'app' => [
        'name' => $env['APP_NAME'] ?? 'My Portfolio',
        'url' => $env['APP_URL'] ?? 'http://localhost/Tamim',
        'debug' => $env['APP_DEBUG'] ?? false,
        'timezone' => 'Asia/Dhaka',
        'locale' => 'en',
    ],
    'database' => require __DIR__ . '/database.php',
    'session' => [
        'name' => 'portfolio_session',
        'lifetime' => 7200, // 2 hours
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutes
        'password_min_length' => 8,
    ],
    'upload' => [
        'path' => __DIR__ . '/../assets/uploads',
        'url' => '/Tamim/assets/uploads',
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'image_max_width' => 1920,
        'image_max_height' => 1080,
    ],
    'pagination' => [
        'per_page' => 10,
    ],
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
    ],
];