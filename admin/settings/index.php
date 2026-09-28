<?php
/**
 * Admin Settings Index
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Website Settings';
$currentPage = 'settings';

$groups = ['general', 'hero', 'about', 'theme', 'seo'];
$settings = [];

foreach ($groups as $group) {
    $settings[$group] = db_all('SELECT * FROM settings WHERE group_name = ? ORDER BY sort_order', [$group]);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    error_log("POST received, CSRF: " . ($_POST[config('security.csrf_token_name')] ?? 'missing'));
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        error_log("CSRF verification failed");
        redirect('/admin/settings/', 'Invalid CSRF token.', 'danger');
    }
    
    error_log("CSRF verified, processing settings");
    
    foreach ($_POST as $key => $value) {
        if ($key === config('security.csrf_token_name') || $key === 'save_settings') continue;
        
        $setting = db_one('SELECT id, setting_type FROM settings WHERE setting_key = ?', [$key]);
        if ($setting) {
            if ($setting['setting_type'] === 'json') {
                $value = json_encode($value);
            }
            db_execute('UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?', [$value, $key]);
        }
    }
    
    // Handle file uploads
    $uploadFields = ['profile_image', 'favicon', 'logo'];
    foreach ($uploadFields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $result = upload_image($_FILES[$field], 'settings');
            if ($result['success']) {
                db_execute('UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?', [$result['filename'], $field]);
            }
        }
    }
    
    redirect('/admin/settings/', 'Settings saved successfully!');
    error_log("Redirect called");
}

require __DIR__ . '/../../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
</div>

<form method="POST" enctype="multipart/form-data" class="settings-form" data-validate>
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- Settings Tabs -->
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header">
                    <h6 class="mb-0">Settings Groups</h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($groups as $index => $group): ?>
                        <a class="list-group-item list-group-item-action <?= $index === 0 ? 'active' : '' ?>" 
                           href="#tab-<?= $group ?>" data-bs-toggle="list">
                            <i class="fas fa-<?= $group === 'general' ? 'cog' : ($group === 'hero' ? 'rocket' : ($group === 'about' ? 'user' : ($group === 'theme' ? 'palette' : 'search'))) ?> me-2"></i>
                            <?= ucfirst($group) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Settings Content -->
        <div class="col-lg-9">
            <div class="tab-content">
                <?php foreach ($groups as $index => $group): ?>
                    <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="tab-<?= $group ?>" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><?= ucfirst($group) ?> Settings</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($settings[$group] as $setting): ?>
                                    <div class="mb-4">
                                        <label class="form-label"><?= e($setting['label']) ?>
                                            <?php if ($setting['description']): ?>
                                                <span class="text-muted ms-2" data-bs-toggle="tooltip" title="<?= e($setting['description']) ?>">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </label>
                                        
                                        <?php if ($setting['setting_type'] === 'image'): ?>
                                            <div class="row g-3 align-items-center">
                                                <div class="col-auto">
                                                    <?php if ($setting['setting_value']): ?>
                                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/' . ($setting['setting_key'] === 'favicon' ? 'settings' : 'settings') . '/' . $setting['setting_value']) ?>" 
                                                             alt="" style="max-width: 80px; max-height: 80px;" class="rounded border">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col">
                                                    <input type="file" class="form-control" name="<?= $setting['setting_key'] ?>" accept="image/*">
                                                    <div class="form-text">Leave empty to keep current image. Max 5MB. JPG, PNG, WebP.</div>
                                                </div>
                                            </div>
                                        <?php elseif ($setting['setting_type'] === 'color'): ?>
                                            <div class="input-group" style="max-width: 300px;">
                                                <input type="color" class="form-control form-control-color" name="<?= $setting['setting_key'] ?>" value="<?= e($setting['setting_value']) ?>" title="Choose color">
                                                <input type="text" class="form-control" name="<?= $setting['setting_key'] ?>_hex" value="<?= e($setting['setting_value']) ?>" placeholder="#RRGGBB">
                                            </div>
                                        <?php elseif ($setting['setting_type'] === 'textarea'): ?>
                                            <textarea class="form-control" name="<?= $setting['setting_key'] ?>" rows="4"><?= e($setting['setting_value']) ?></textarea>
                                        <?php elseif ($setting['setting_type'] === 'json'): ?>
                                            <textarea class="form-control font-monospace" name="<?= $setting['setting_key'] ?>" rows="6"><?= e($setting['setting_value']) ?></textarea>
                                            <div class="form-text">Valid JSON format required.</div>
                                        <?php else: ?>
                                            <input type="text" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= e($setting['setting_value']) ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4">
                <button type="submit" name="save_settings" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save All Settings
                </button>
                <a href="/admin/dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </div>
    </div>
</form>

<script>
// Sync color picker with hex input
document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
    const hexInput = document.querySelector('input[name="' + colorInput.name + '_hex"]');
    if (hexInput) {
        colorInput.addEventListener('input', function() {
            hexInput.value = this.value;
        });
        hexInput.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
            }
        });
    }
});
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
