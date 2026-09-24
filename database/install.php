<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/init.php';

$installedFile = __DIR__ . '/installed';
$installed = is_file($installedFile);
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
        try {
            if (!tamim_verify_csrf() || ($_POST['confirm_install'] ?? '') !== '1') {
                throw new RuntimeException('The installation request could not be verified.');
            }

        $databaseName = tamim_env('DB_NAME', 'tamim_portfolio');
        if (!preg_match('/^[A-Za-z0-9_]+$/', $databaseName)) {
            throw new RuntimeException('The database name contains unsupported characters.');
        }

        $server = tamim_pdo(null);
        $server->exec('CREATE DATABASE IF NOT EXISTS `' . $databaseName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $pdo = tamim_pdo($databaseName);
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $seed = file_get_contents(__DIR__ . '/seed.sql');

        if ($schema === false || $seed === false) {
            throw new RuntimeException('Installation files could not be read.');
        }

        $pdo->exec($schema);
        $pdo->exec($seed);

        $email = filter_var(tamim_env('ADMIN_EMAIL', 'admin@example.com'), FILTER_VALIDATE_EMAIL);
        $password = tamim_env('ADMIN_PASSWORD', 'ChangeMe123!');
        if ($email === false || strlen($password) < 8) {
            throw new RuntimeException('The administrator email or password is invalid.');
        }

        $statement = $pdo->prepare('INSERT IGNORE INTO admins (name, email, password_hash) VALUES (:name, :email, :passwordHash)');
        $statement->execute([
            ':name' => 'Tamim',
            ':email' => $email,
            ':passwordHash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        file_put_contents($installedFile, date(DATE_ATOM) . PHP_EOL, LOCK_EX);
        $message = 'Installation completed. You can now sign in to the admin panel.';
        $messageType = 'success';
    } catch (Throwable $error) {
        $message = $error->getMessage();
        $messageType = 'error';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install Tamim Portfolio</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="install-body">
<main class="install-shell">
    <section class="install-card">
        <p class="eyebrow">Tamim Portfolio</p>
        <h1>Install your website</h1>
        <p class="muted">This creates the database tables and starter content. Keep the installer private after setup.</p>

        <?php if ($message !== ''): ?>
            <div class="notice notice-<?php echo tamim_e($messageType); ?>" role="status"><?php echo tamim_e($message); ?></div>
        <?php endif; ?>

        <?php if ($installed): ?>
            <div class="empty-state"><strong>Already installed.</strong><p>Visit <a href="/admin/login">the admin login</a> or <a href="/">the website</a>.</p></div>
        <?php else: ?>
            <form method="post" class="stacked-form">
                <?php echo tamim_csrf_field(); ?>
                <input type="hidden" name="confirm_install" value="1">
                <label class="check-row"><input type="checkbox" name="confirm" value="install" required> I have configured the database settings and understand that this will create tables.</label>
                <button type="submit" class="button button-primary">Install website</button>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
