<?php
/**
 * Production Debug Script - Shows actual 500 error details
 * Access: https://traveleyebangla.com/debug.php
 * DELETE AFTER USE!
 */

// Enable full error display
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>Debug Info</title>";
echo "<style>body{font-family:monospace;margin:20px;background:#1e1e1e;color:#d4d4d4;padding:20px} ";
echo "h1{color:#4ec9b0} .ok{color:#4ec9b0} .err{color:#f44747} .warn{color:#cca700} ";
echo "pre{background:#2d2d2d;padding:15px;border-radius:5px;overflow:auto} ";
echo ".box{border:1px solid #3c3c3c;padding:15px;margin:10px 0;border-radius:5px;background:#252526}</style>";
echo "</head><body>";

echo "<h1>🔍 Traveleye Bangla - Debug Report</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP: " . PHP_VERSION . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";

// 1. Check .env
echo "<div class='box'><h2>1. .env File</h2>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "<p class='ok'>✅ .env exists</p>";
    $env = parse_ini_file($envPath, true);
    echo "<pre>" . htmlspecialchars(print_r([
        'APP_NAME' => $env['APP_NAME'] ?? 'NOT SET',
        'APP_URL' => $env['APP_URL'] ?? 'NOT SET',
        'APP_DEBUG' => $env['APP_DEBUG'] ?? 'NOT SET',
        'DB_HOST' => $env['DB_HOST'] ?? 'NOT SET',
        'DB_PORT' => $env['DB_PORT'] ?? 'NOT SET',
        'DB_NAME' => $env['DB_NAME'] ?? 'NOT SET',
        'DB_USER' => $env['DB_USER'] ?? 'NOT SET',
        'DB_PASS' => isset($env['DB_PASS']) ? '***SET***' : 'NOT SET (EMPTY)',
    ], true)) . "</pre>";
} else {
    echo "<p class='err'>❌ .env NOT FOUND!</p>";
    echo "<p>Copy .env.example to .env and configure</p>";
}
echo "</div>";

// 2. PHP Extensions
echo "<div class='box'><h2>2. Required Extensions</h2>";
$exts = ['pdo', 'pdo_mysql', 'mbstring', 'gd', 'json', 'openssl', 'curl', 'fileinfo', 'zip'];
foreach ($exts as $ext) {
    $loaded = extension_loaded($ext);
    echo "<p class='" . ($loaded ? 'ok' : 'err') . "'>" . ($loaded ? '✅' : '❌') . " $ext</p>";
}
echo "</div>";

// 3. Database Connection
echo "<div class='box'><h2>3. Database Connection</h2>";
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath, true);
    try {
        $dsn = "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        echo "<p class='ok'>✅ Connected to {$env['DB_NAME']}</p>";
        
        // Check tables
        $tables = ['admins', 'settings', 'social_links', 'skills', 'services', 'projects', 'messages', 'blog_categories', 'blog_posts', 'live_chats', 'live_chat_messages'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                $count = $stmt->fetchColumn();
                echo "<p class='ok'>✅ Table $table: $count rows</p>";
            } catch (PDOException $e) {
                echo "<p class='err'>❌ Table $table: " . $e->getMessage() . "</p>";
            }
        }
        
        // Check admin
        $stmt = $pdo->prepare("SELECT id, email FROM admins LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();
        if ($admin) {
            echo "<p class='ok'>✅ Admin exists: {$admin['email']}</p>";
        } else {
            echo "<p class='err'>❌ No admin user found</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p class='err'>❌ DB Connection Failed: " . $e->getMessage() . "</p>";
        echo "<p class='warn'>Check DB_HOST, DB_NAME, DB_USER, DB_PASS in .env</p>";
    }
}
echo "</div>";

// 4. File Permissions
echo "<div class='box'><h2>4. File Permissions</h2>";
$paths = [
    'assets/uploads' => 0755,
    'assets/uploads/settings' => 0755,
    'assets/uploads/projects' => 0755,
    'assets/uploads/blog' => 0755,
];
foreach ($paths as $path => $perm) {
    $full = __DIR__ . '/' . $path;
    if (!is_dir($full)) {
        @mkdir($full, 0755, true);
    }
    $writable = is_writable($full);
    $perms = substr(sprintf('%o', fileperms($full)), -4);
    echo "<p class='" . ($writable ? 'ok' : 'err') . "'>" . ($writable ? '✅' : '❌') . " $path (perms: $perms)</p>";
}
echo "</div>";

// 5. Key Files
echo "<div class='box'><h2>5. Key Files</h2>";
$files = ['index.php', 'config/app.php', 'config/database.php', 'includes/bootstrap.php', 'includes/functions.php', '.htaccess'];
foreach ($files as $f) {
    $exists = file_exists(__DIR__ . '/' . $f);
    echo "<p class='" . ($exists ? 'ok' : 'err') . "'>" . ($exists ? '✅' : '❌') . " $f</p>";
}
echo "</div>";

// 6. mod_rewrite
echo "<div class='box'><h2>6. Apache Modules</h2>";
$mods = ['mod_rewrite', 'mod_headers', 'mod_deflate', 'mod_expires'];
if (function_exists('apache_get_modules')) {
    $loaded = apache_get_modules();
    foreach ($mods as $mod) {
        $has = in_array($mod, $loaded);
        echo "<p class='" . ($has ? 'ok' : 'warn') . "'>" . ($has ? '✅' : '⚠️') . " $mod</p>";
    }
} else {
    echo "<p class='warn'>⚠️ apache_get_modules() not available (probably not Apache or restricted)</p>";
}
echo "</div>";

// 7. Try Bootstrap
echo "<div class='box'><h2>7. Bootstrap Test</h2>";
try {
    require __DIR__ . '/includes/bootstrap.php';
    echo "<p class='ok'>✅ bootstrap.php loaded</p>";
    
    // Test config
    $appName = config('app.name');
    echo "<p class='ok'>✅ config() works: app.name = $appName</p>";
    
    // Test DB function
    $pdo = db();
    echo "<p class='ok'>✅ db() works - PDO connected</p>";
    
} catch (Throwable $e) {
    echo "<p class='err'>❌ Bootstrap failed: " . $e->getMessage() . "</p>";
    echo "<pre class='err'>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

// 8. Error Log
echo "<div class='box'><h2>8. Recent PHP Error Log</h2>";
$logFile = '/home/traveleyeba/logs/php.error.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recent = array_slice($lines, -20);
    echo "<pre>" . htmlspecialchars(implode('', $recent)) . "</pre>";
} else {
    echo "<p class='warn'>Log file not found at $logFile</p>";
    echo "<p>Check cPanel > Errors or MultiPHP INI Editor for correct path</p>";
}
echo "</div>";

echo "<hr><p><strong>⚠️ DELETE THIS FILE AFTER DEBUGGING!</strong> <code>rm debug.php</code></p>";
echo "</body></html>";