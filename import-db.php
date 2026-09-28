<?php
/**
 * Database Import Script
 * Run ONCE after uploading to cPanel to create tables
 * Access via: https://traveleyebangla.com/import-db.php
 * DELETE AFTER USE!
 */

header('Content-Type: text/html; charset=utf-8');

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    die('<h1>Confirm Required</h1><p>Add <code>?confirm=yes</code> to URL to run import.</p>');
}

require __DIR__ . '/includes/bootstrap.php';

echo "<h1>Database Import</h1>";
echo "<p>Reading database.sql...</p>";

$sql = file_get_contents(__DIR__ . '/database.sql');
if (!$sql) {
    die('<div style="color:red">❌ Could not read database.sql</div>');
}

// Get DB name from .env and replace in SQL
$env = parse_ini_file(__DIR__ . '/.env', true);
$dbName = $env['DB_NAME'] ?? 'traveleyeba_db';
$sql = str_replace('`portfolio_db`', '`' . $dbName . '`', $sql);
$sql = str_replace('portfolio_db', $dbName, $sql);

// Split by semicolon (simple approach)
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$failed = 0;
$errors = [];

foreach ($statements as $stmt) {
    if (empty($stmt) || str_starts_with($stmt, '--')) continue;
    
    try {
        db()->exec($stmt);
        $success++;
    } catch (PDOException $e) {
        // Ignore "already exists" errors
        if ($e->getCode() !== '42S01' && $e->getCode() !== '23000') {
            $failed++;
            $errors[] = $e->getMessage();
        }
    }
}

echo "<div style='color:green'>✅ Executed: $success statements</div>";
if ($failed > 0) {
    echo "<div style='color:orange'>⚠️ Failed (non-critical): $failed</div>";
    echo "<ul>";
    foreach ($errors as $err) echo "<li>$err</li>";
    echo "</ul>";
} else {
    echo "<div style='color:green'>✅ All statements executed successfully!</div>";
}

// Verify key tables
echo "<h2>Verification</h2>";
$tables = ['admins', 'settings', 'social_links', 'skills', 'services', 'projects', 'messages', 'blog_categories', 'blog_posts'];
foreach ($tables as $table) {
    try {
        $count = db_one("SELECT COUNT(*) as c FROM `$table`")['c'] ?? 0;
        echo "<div>✅ $table: $count rows</div>";
    } catch (PDOException $e) {
        echo "<div style='color:red'>❌ $table: " . $e->getMessage() . "</div>";
    }
}

echo "<hr><p><strong>Import complete!</strong> Delete this file: <code>rm import-db.php</code></p>";