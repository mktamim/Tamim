<?php
/**
 * Admin About Settings
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$pageTitle = 'About Settings';
$currentPage = 'about';

$settings = db_all('SELECT * FROM settings WHERE group_name = "about" ORDER BY sort_order');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_about'])) {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        redirect('/Tamim/admin/about/', 'Invalid CSRF token.', 'danger');
    }
    
    foreach ($_POST as $key => $value) {
        if ($key === config('security.csrf_token_name') || $key === 'save_about') continue;
        
        $setting = db_one('SELECT id, setting_type FROM settings WHERE setting_key = ?', [$key]);
        if ($setting) {
            if ($setting['setting_type'] === 'json') {
                $value = json_encode($value);
            }
            db_execute('UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?', [$value, $key]);
        }
    }
    
    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $result = upload_image($_FILES['profile_image'], 'settings');
        if ($result['success']) {
            db_execute('UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = "profile_image"', [$result['filename']]);
        }
    }
    
    redirect('/Tamim/admin/about/', 'About settings saved successfully!');
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
</div>

<form method="POST" enctype="multipart/form-data" class="card" data-validate>
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-8">
                <?php foreach ($settings as $setting): ?>
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
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . $setting['setting_value']) ?>" alt="" style="max-width: 100px; max-height: 100px;" class="rounded border">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;"><i class="fas fa-image text-muted"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col">
                                    <input type="file" class="form-control" name="<?= $setting['setting_key'] ?>" accept="image/*">
                                    <div class="form-text">Leave empty to keep current. Max 5MB.</div>
                                </div>
                            </div>
                        <?php elseif ($setting['setting_type'] === 'textarea'): ?>
                            <textarea class="form-control" name="<?= $setting['setting_key'] ?>" rows="5"><?= e($setting['setting_value']) ?></textarea>
                        <?php else: ?>
                            <input type="text" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= e($setting['setting_value']) ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Profile Image</h6>
                        <div class="text-center mb-3">
                            <?php 
                            $profileImg = setting('profile_image');
                            if ($profileImg): ?>
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . $profileImg) ?>" alt="" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 60px; font-weight: 700;">
                                    <?= strtoupper(substr(setting('developer_name', 'D'), 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control" name="profile_image" accept="image/*">
                        <div class="form-text">Recommended: 400x400px. Max 5MB.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" name="save_about" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save About Settings
        </button>
        <a href="/Tamim/admin/dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>