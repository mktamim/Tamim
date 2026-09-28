<?php
/**
 * Database Configuration
 * Portfolio Website - Config
 */

$env = parse_ini_file(__DIR__ . '/../.env', true) ?: [];

return [
    'host' => $env['DB_HOST'] ?? '127.0.0.1',
    'port' => $env['DB_PORT'] ?? '3306',
    'dbname' => $env['DB_NAME'] ?? 'traveleyeba_db',
    'username' => $env['DB_USER'] ?? 'traveleyeba_traveleyeba',
    'password' => $env['DB_PASS'] ?? '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];