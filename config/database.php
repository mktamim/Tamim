<?php
declare(strict_types=1);

function tamim_env(string $key, ?string $default = null): ?string
{
    static $values = null;

    if ($values === null) {
        $values = [];
        $file = dirname(__DIR__) . '/.env';

        if (is_file($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            if ($lines !== false) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }

                    $parts = explode('=', $line, 2);
                    if (count($parts) !== 2) {
                        continue;
                    }

                    $name = trim($parts[0]);
                    $value = trim($parts[1]);
                    if (strlen($value) >= 2 && in_array($value[0], ['"', "'"], true) && $value[strlen($value) - 1] === $value[0]) {
                        $value = substr($value, 1, -1);
                    }
                    $values[$name] = $value;
                }
            }
        }
    }

    return array_key_exists($key, $values) ? $values[$key] : $default;
}

function tamim_pdo(?string $database = null): PDO
{
    $host = tamim_env('DB_HOST', '127.0.0.1');
    $port = tamim_env('DB_PORT', '3306');
    $username = tamim_env('DB_USER', 'root');
    $password = tamim_env('DB_PASS', '');
    $charset = 'utf8mb4';
    $defaultDatabase = tamim_env('DB_NAME', 'tamim_portfolio');

    $dsn = "mysql:host={$host};port={$port};charset={$charset}";
    $targetDatabase = ($database !== null && $database !== '') ? $database : $defaultDatabase;
    if ($targetDatabase !== '') {
        $dsn .= ";dbname={$targetDatabase}";
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    return new PDO($dsn, $username, $password, $options);
}
