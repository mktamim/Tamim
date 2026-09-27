<?php
/**
 * Admin Homepage Settings
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$pageTitle = 'Homepage Settings';
$currentPage = 'homepage';

$sections = ['hero' => 'Hero Section', 'about' => 'About Section', 'cta' => 'Call to Action'];
$settings = [];

foreach ($sections as $key => $label) {
    $settings[$key] = db_all('SELECT * FROM settings WHERE group_name = ? ORDER BY sort_order', [$key]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_homepage'])) {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        redirect('/Tamim/admin/homepage/', 'Invalid CSRF token.', 'danger');
    }
    
    foreach ($_POST as $key => $value) {
        if ($key === config('security.csrf_token_name') || $key === 'save_homepage') continue;
        
        $setting = db_one('SELECT id, setting_type FROM settings WHERE setting_key = ?', [$key]);
        if ($setting) {
            if ($setting['setting_type'] === 'json') {
                $value = json_encode($value);
            }
            db_execute('UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?', [$value, $key]);
        }
    }
    
    // Handle image uploads
    $uploadFields = ['profile_image'];
    foreach ($uploadFields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $result = upload_image($_FILES[$field], 'settings');
            if ($result['success']) {
                db_execute('UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?', [$result['filename'], $field]);
            }
        }
    }
    
    redirect('/Tamim/admin/homepage/', 'Homepage settings saved successfully!');
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
</div>

<form method="POST" enctype="multipart/form-data" class="settings-form" data-validate>
    <?= csrf_field() ?>
    
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-hero" role="tab">Hero Section</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-about" role="tab">About Section</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cta" role="tab">CTA Buttons</button>
        </li>
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-hero" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <?php foreach ($settings['hero'] as $setting): ?>
                        <div class="mb-4">
                            <label class="form-label"><?= e($setting['label']) ?></label>
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
                                <textarea class="form-control" name="<?= $setting['setting_key'] ?>" rows="4"><?= e($setting['setting_value']) ?></textarea>
                            <?php else: ?>
                                <input type="text" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= e($setting['setting_value']) ?>">
                            <?php endif; ?>
                            <?php if ($setting['description']): ?>
                                <div class="form-text"><?= e($setting['description']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="tab-about" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <?php foreach ($settings['about'] as $setting): ?>
                        <div class="mb-4">
                            <label class="form-label"><?= e($setting['label']) ?></label>
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
                                <textarea class="form-control" name="<?= $setting['setting_key'] ?>" rows="4"><?= e($setting['setting_value']) ?></textarea>
                            <?php else: ?>
                                <input type="text" class="form-control" name="<?= $setting['setting_key'] ?>" value="<?= e($setting['setting_value']) ?>">
                            <?php endif; ?>
                            <?php if ($setting['description']): ?>
                                <div class="form-text"><?= e($setting['description']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="tab-cta" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4">Call to Action Buttons</h6>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Primary CTA</h6>
                                    <?php 
                                    $cta1 = db_one('SELECT * FROM settings WHERE setting_key = "hero_cta_text"');
                                    $cta1Link = db_one('SELECT * FROM settings WHERE setting_key = "hero_cta_link"');
                                    ?>
                                    <div class="mb-3">
                                        <label class="form-label">Button Text</label>
                                        <input type="text" class="form-control" name="hero_cta_text" value="<?= e($cta1['setting_value'] ?? 'View My Work') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Button Link</label>
                                        <input type="text" class="form-control" name="hero_cta_link" value="<?= e($cta1Link['setting_value'] ?? '#projects') ?>" placeholder="#projects or /contact">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Secondary CTA</h6>
                                    <?php 
                                    $cta2 = db_one('SELECT * FROM settings WHERE setting_key = "hero_cta2_text"');
                                    $cta2Link = db_one('SELECT * FROM settings WHERE setting_key = "hero_cta2_link"');
                                    ?>
                                    <div class="mb-3">
                                        <label class="form-label">Button Text</label>
                                        <input type="text" class="form-control" name="hero_cta2_text" value="<?= e($cta2['setting_value'] ?? 'Hire Me') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Button Link</label>
                                        <input type="text" class="form-control" name="hero_cta2_link" value="<?= e($cta2Link['setting_value'] ?? '#contact') ?>" placeholder="#contact or /contact">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <button type="submit" name="save_homepage" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save Homepage Settings
        </button>
        <a href="/Tamim/admin/dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>