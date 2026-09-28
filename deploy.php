<?php
/**
 * Deployment Health Check Script
 * Access via: https://traveleyebangla.com/deploy.php
 * DELETE THIS FILE AFTER DEPLOYMENT!
 */

header('Content-Type: text/html; charset=utf-8');

$checks = [];
$errors = [];
$warnings = [];

function check($name, $condition, $message = '', $critical = true) {
    global $checks, $errors, $warnings;
    $result = ['name' => $name, 'pass' => $condition, 'message' => $message];
    $checks[] = $result;
    if (!$condition) {
        if ($critical) {
            $errors[] = "$name: $message";
        } else {
            $warnings[] = "$name: $message";
        }
    }
    return $condition;
}

// 1. PHP Version
check('PHP Version >= 8.1', version_compare(PHP_VERSION, '8.1.0', '>='), 
    "Current: " . PHP_VERSION . " (Required: 8.1+)");

// 2. Required Extensions
$requiredExt = ['pdo', 'pdo_mysql', 'mbstring', 'gd', 'json', 'openssl', 'curl', 'fileinfo'];
foreach ($requiredExt as $ext) {
    check("Extension: $ext", extension_loaded($ext), 
        extension_loaded($ext) ? 'Loaded' : 'MISSING - Install via cPanel > Select PHP Version');
}

// 3. File Permissions
$paths = [
    'assets/uploads' => 0755,
    'assets/uploads/settings' => 0755,
    'assets/uploads/projects' => 0755,
    'assets/uploads/blog' => 0755,
];
foreach ($paths as $path => $perm) {
    $fullPath = __DIR__ . '/' . $path;
    if (!is_dir($fullPath)) {
        @mkdir($fullPath, 0755, true);
    }
    $writable = is_writable($fullPath);
    check("Writable: $path", $writable, 
        $writable ? 'OK' : 'NOT WRITABLE - chmod 755 via File Manager');
}

// 4. .env File
$envPath = __DIR__ . '/.env';
$envExists = file_exists($envPath);
check('.env file exists', $envExists, 
    $envExists ? 'Found' : 'MISSING - Copy .env.example to .env and configure');

// 5. Database Connection
if ($envExists) {
    $env = parse_ini_file($envPath, true);
    try {
        $dsn = "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        check('Database Connection', true, 'Connected successfully');
        
        // Check tables
        $tables = ['admins', 'settings', 'social_links', 'skills', 'services', 'projects', 'messages'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
                check("Table: $table", true, 'Exists');
            } catch (PDOException $e) {
                check("Table: $table", false, 'MISSING - Import database.sql', false);
            }
        }
        
        // Check admin user
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$env['ADMIN_EMAIL'] ?? 'admin@example.com']);
        $adminExists = $stmt->fetchColumn() !== false;
        check('Admin User Exists', $adminExists, $adminExists ? 'Found' : 'Run database.sql to create admin');
        
    } catch (PDOException $e) {
        check('Database Connection', false, 'FAILED: ' . $e->getMessage());
    }
}

// 6. .htaccess
$htaccessPath = __DIR__ . '/.htaccess';
check('.htaccess exists', file_exists($htaccessPath), 
    file_exists($htaccessPath) ? 'Found' : 'MISSING');

// 7. Key PHP Files
$keyFiles = ['index.php', 'config/app.php', 'config/database.php', 'includes/bootstrap.php', 'includes/functions.php'];
foreach ($keyFiles as $file) {
    check("File: $file", file_exists(__DIR__ . '/' . $file), '');
}

// 8. mod_rewrite (indirect check)
$modRewrite = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());
check('mod_rewrite enabled', $modRewrite, 
    $modRewrite ? 'Enabled' : 'May not be enabled - Check Apache config', false);

// 9. SSL/HTTPS
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
         (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
check('HTTPS Active', $https, $https ? 'Yes' : 'No - Enable SSL in cPanel', false);

// Output Results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Health Check</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f8f9fa; }
        .card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h1 { color: #1f2937; margin-bottom: 8px; }
        .subtitle { color: #6b7280; margin-bottom: 24px; }
        .status { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; margin: 8px 0; }
        .status.pass { background: #ecfdf5; border-left: 4px solid #10b981; }
        .status.fail { background: #fef2f2; border-left: 4px solid #ef4444; }
        .status.warn { background: #fffbeb; border-left: 4px solid #f59e0b; }
        .status-name { font-weight: 600; flex: 1; }
        .status-msg { color: #6b7280; font-size: 14px; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-ok { background: #d1fae5; color: #065f46; }
        .badge-fail { background: #fee2e2; color: #991b1b; }
        .badge-warn { background: #fef3c7; color: #92400e; }
        .summary { display: flex; gap: 16px; margin-bottom: 24px; }
        .summary-box { flex: 1; padding: 16px; border-radius: 10px; text-align: center; }
        .summary-errors { background: #fef2f2; color: #991b1b; }
        .summary-warnings { background: #fffbeb; color: #92400e; }
        .summary-ok { background: #ecfdf5; color: #065f46; }
        .summary-number { font-size: 32px; font-weight: 700; }
        .alert { padding: 16px; border-radius: 8px; margin-bottom: 20px; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #ecfdf5; border: 1px solid #bbf7d0; color: #065f46; }
        .cmd { background: #1f2937; color: #e5e7eb; padding: 12px 16px; border-radius: 8px; font-family: monospace; font-size: 13px; overflow-x: auto; margin: 8px 0; }
        .section-title { font-size: 18px; font-weight: 600; margin: 24px 0 12px; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 Deployment Health Check</h1>
        <p class="subtitle">Traveleye Bangla - Server Requirements Verification</p>
        
        <div class="summary">
            <div class="summary-box summary-errors">
                <div class="summary-number"><?= count($errors) ?></div>
                <div>Critical Errors</div>
            </div>
            <div class="summary-box summary-warnings">
                <div class="summary-number"><?= count($warnings) ?></div>
                <div>Warnings</div>
            </div>
            <div class="summary-box summary-ok">
                <div class="summary-number"><?= count(array_filter($checks, fn($c) => $c['pass'])) ?></div>
                <div>Passed</div>
            </div>
        </div>
        
        <?php if (count($errors) > 0): ?>
        <div class="alert alert-danger">
            <strong>❌ Deployment Not Ready</strong> - Fix critical errors above before going live.
        </div>
        <?php elseif (count($warnings) > 0): ?>
        <div class="alert alert-danger">
            <strong>⚠️ Ready with Warnings</strong> - Site will work but review warnings.
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <strong>✅ All Checks Passed</strong> - Ready for production!
        </div>
        <?php endif; ?>
        
        <div class="section-title">📋 Detailed Checks</div>
        <?php foreach ($checks as $c): ?>
        <div class="status <?= $c['pass'] ? 'pass' : ($errors && in_array($c['name'] . ': ' . $c['message'], $errors) ? 'fail' : 'warn') ?>">
            <span class="status-name"><?= htmlspecialchars($c['name']) ?></span>
            <span class="badge <?= $c['pass'] ? 'badge-ok' : 'badge-fail' ?>"><?= $c['pass'] ? '✓ PASS' : '✗ FAIL' ?></span>
            <?php if ($c['message']): ?>
            <span class="status-msg"><?= htmlspecialchars($c['message']) ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <div class="section-title">🔧 Quick Fix Commands</div>
        <div class="cmd"># Fix file permissions (run in cPanel Terminal or SSH)<br>
chmod -R 755 assets/uploads<br>
find assets/uploads -type f -exec chmod 644 {} \;</div>

        <div class="cmd"># Import database (replace with your credentials)<br>
mysql -u traveleyeba_traveleyeba -p traveleyeba_db < database.sql</div>

        <div class="cmd"># Check PHP extensions in cPanel<br>
Software > Select PHP Version > Check: pdo_mysql, mbstring, gd, fileinfo, curl, json, openssl</div>

        <div class="section-title">⚠️ SECURITY NOTICE</div>
        <div class="alert alert-danger">
            <strong>DELETE THIS FILE AFTER DEPLOYMENT!</strong><br>
            <code>rm deploy.php</code><br>
            This script exposes server configuration details.
        </div>
    </div>
</body>
</html>