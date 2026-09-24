<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | <?php echo tamim_e(tamim_setting('site_name', 'Tamim')); ?> Admin</title>
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
            <div class="admin-topbar-title">Settings</div>
            <div class="admin-topbar-actions">
                <a class="admin-site-link" href="/">View site</a>
                <span class="admin-admin-name"><?php echo tamim_e($admin['name'] ?? 'Admin'); ?></span>
            </div>
        </header>
        <main id="main-content" class="admin-main" tabindex="-1">
            <?php foreach (tamim_flashes() as $flash): ?>
                <div class="admin-notice admin-notice <?php echo tamim_e($flash['type']); ?>" role="status"><?php echo tamim_e($flash['message']); ?></div>
            <?php endforeach; ?>

            <section class="admin-page-heading">
                <div>
                    <p class="admin-eyebrow">Site configuration</p>
                    <h1>Settings</h1>
                    <p class="admin-page-intro">Manage the content and contact details used throughout the public website.</p>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel-heading">
                    <div><p class="admin-eyebrow">Add a setting</p><h2>Create a site value</h2></div>
                </div>
                <form class="admin-form admin-form-grid" method="post" action="">
                    <?php echo tamim_csrf_field(); ?>
                    <input type="hidden" name="action" value="create">
                    <label>
                        <span>Key</span>
                        <input type="text" name="setting_key" required pattern="[A-Za-z][A-Za-z0-9_]{1,79}" placeholder="example_setting" maxlength="80">
                        <small>Letters, numbers and underscores. Start with a letter.</small>
                    </label>
                    <label class="admin-form-full">
                        <span>Value</span>
                        <textarea name="setting_value" rows="4" required maxlength="100000" placeholder="Setting value"></textarea>
                    </label>
                    <div class="admin-form-actions admin-form-full">
                        <button class="admin-button admin-button-primary" type="submit">Create setting <span>+</span></button>
                    </div>
                </form>
            </section>

            <section class="admin-panel admin-panel-spaced">
                <div class="admin-panel-heading">
                    <div><p class="admin-eyebrow">All settings</p><h2>Site settings</h2></div>
                    <span class="admin-panel-count"><?php echo count($settings); ?> total</span>
                </div>
                <?php if ($settings === []): ?>
                    <div class="admin-empty-small">No settings have been created yet.</div>
                <?php else: ?>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr><th>Key</th><th>Value</th><th>Updated</th><th><span class="sr-only">Actions</span></th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($settings as $setting): ?>
                                    <tr>
                                        <td><code><?php echo tamim_e($setting['setting_key']); ?></code></td>
                                        <td><div class="admin-setting-value"><?php echo nl2br(tamim_e($setting['setting_value'])); ?></div></td>
                                        <td><?php echo tamim_e(tamim_format_date($setting['updated_at'], 'M d, Y H:i')); ?></td>
                                        <td>
                                            <div class="admin-row-actions">
                                                <button class="admin-icon-button" type="button" data-setting-key="<?php echo tamim_e($setting['setting_key']); ?>" data-setting-value="<?php echo tamim_e($setting['setting_value']); ?>" aria-label="Edit <?php echo tamim_e($setting['setting_key']); ?>">Edit</button>
                                                <form method="post" action="">
                                                    <?php echo tamim_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="setting_key" value="<?php echo tamim_e($setting['setting_key']); ?>">
                                                    <button class="admin-icon-button admin-icon-button-danger" type="submit" onclick="return confirm('Delete this setting?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</div>
<dialog class="admin-dialog" id="setting-editor">
    <form class="admin-form" method="post" action="">
        <?php echo tamim_csrf_field(); ?>
        <input type="hidden" name="action" value="update">
        <div class="admin-dialog-heading">
            <div><p class="admin-eyebrow">Edit setting</p><h2>Update value</h2></div>
            <button class="admin-close-button" type="button" data-close-dialog aria-label="Close">×</button>
        </div>
        <label>
            <span>Key</span>
            <input type="text" name="setting_key" id="setting-editor-key" readonly>
        </label>
        <label>
            <span>Value</span>
            <textarea name="setting_value" id="setting-editor-value" rows="7" required maxlength="100000"></textarea>
        </label>
        <div class="admin-dialog-actions">
            <button class="admin-button admin-button-secondary" type="button" data-close-dialog>Cancel</button>
            <button class="admin-button admin-button-primary" type="submit">Save setting</button>
        </div>
    </form>
</dialog>
<script>
(function () {
    var dialog = document.getElementById('setting-editor');
    var key = document.getElementById('setting-editor-key');
    var value = document.getElementById('setting-editor-value');
    document.querySelectorAll('[data-setting-key]').forEach(function (button) {
        button.addEventListener('click', function () {
            key.value = button.getAttribute('data-setting-key');
            value.value = button.getAttribute('data-setting-value');
            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            }
        });
    });
    document.querySelectorAll('[data-close-dialog]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (typeof dialog.close === 'function') {
                dialog.close();
            }
        });
    });
}());
</script>
</body>
</html>