<?php
declare(strict_types=1);

$pageTitle ??= 'Admin';
$pageDescription ??= 'Tamim portfolio administration';
$siteName = tamim_setting('site_name', 'Tamim');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo tamim_e($pageTitle); ?> | <?php echo tamim_e($siteName); ?> Admin</title>
    <meta name="description" content="<?php echo tamim_e($pageDescription); ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-body">
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="admin-layout">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <div class="admin-workspace">
        <header class="admin-topbar">
            <label class="mobile-menu-toggle" for="admin-navigation" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </label>
            <div class="admin-topbar-title"><?php echo tamim_e($pageTitle); ?></div>
            <div class="admin-topbar-actions">
                <a class="admin-site-link" href="/">View site</a>
                <span class="admin-admin-name"><?php echo tamim_e($admin['name'] ?? 'Admin'); ?></span>
            </div>
        </header>
        <main id="main-content" class="admin-main" tabindex="-1">
            <?php foreach (tamim_flashes() as $flash): ?>
                <div class="admin-notice admin-notice-<?php echo tamim_e($flash['type']); ?>" role="status">
                    <?php echo tamim_e($flash['message']); ?>
                </div>
            <?php endforeach; ?>
