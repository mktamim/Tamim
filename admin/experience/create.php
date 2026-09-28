<?php
/**
 * Admin Experience Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$isEdit = isset($_GET['id']);
$experience = null;

if ($isEdit) {
    $experience = db_one('SELECT * FROM experiences WHERE id = ?', [(int)$_GET['id']]);
    if (!$experience) {
        redirect('/admin/experience/', 'Experience not found.', 'danger');
    }
    $pageTitle = 'Edit Experience';
} else {
    $pageTitle = 'Add Experience';
}

$currentPage = 'experience';
$errors = [];
$formData = [
    'company_name' => '',
    'position' => '',
    'location' => '',
    'start_date' => '',
    'end_date' => '',
    'is_current' => 0,
    'description' => '',
    'achievements' => [],
    'technologies' => [],
    'company_logo' => '',
    'company_url' => '',
    'is_active' => 1,
    'sort_order' => 0,
];

if ($isEdit) {
    $formData = array_merge($formData, $experience);
    if ($experience['achievements']) {
        $formData['achievements'] = json_decode($experience['achievements'], true) ?? [];
    }
    if ($experience['technologies']) {
        $formData['technologies'] = json_decode($experience['technologies'], true) ?? [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $formData = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'position' => trim($_POST['position'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['is_current'] ? null : ($_POST['end_date'] ?? ''),
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? ''),
            'achievements' => array_filter(array_map('trim', $_POST['achievements'] ?? [])),
            'technologies' => array_filter(array_map('trim', $_POST['technologies'] ?? [])),
            'company_logo' => '',
            'company_url' => trim($_POST['company_url'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        
        if (empty($formData['company_name']) || empty($formData['position']) || empty($formData['start_date'])) {
            $errors[] = 'Company name, position, and start date are required.';
        }
        
        if (!$formData['is_current'] && empty($formData['end_date'])) {
            $errors[] = 'End date is required unless this is your current position.';
        }
        
        // Handle logo upload
        if (!empty($_FILES['company_logo']['name'])) {
            $result = upload_image($_FILES['company_logo'], 'experience');
            if ($result['success']) {
                if ($isEdit && $experience['company_logo']) {
                    delete_file('experience/' . $experience['company_logo']);
                }
                $formData['company_logo'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['company_logo'] = $experience['company_logo'];
        }
        
        if (empty($errors)) {
            $achievementsJson = json_encode(array_values($formData['achievements']));
            $techJson = json_encode(array_values($formData['technologies']));
            
            if ($isEdit) {
                $sql = 'UPDATE experiences SET company_name=?, position=?, location=?, start_date=?, end_date=?, is_current=?, description=?, achievements=?, technologies=?, company_logo=?, company_url=?, is_active=?, sort_order=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['company_name'], $formData['position'], $formData['location'], $formData['start_date'], $formData['end_date'], $formData['is_current'], $formData['description'], $achievementsJson, $techJson, $formData['company_logo'], $formData['company_url'], $formData['is_active'], $formData['sort_order'], $experience['id']];
                db_execute($sql, $params);
                redirect('/admin/experience/', 'Experience updated successfully!');
            } else {
                $sql = 'INSERT INTO experiences (company_name, position, location, start_date, end_date, is_current, description, achievements, technologies, company_logo, company_url, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['company_name'], $formData['position'], $formData['location'], $formData['start_date'], $formData['end_date'], $formData['is_current'], $formData['description'], $achievementsJson, $techJson, $formData['company_logo'], $formData['company_url'], $formData['is_active'], $formData['sort_order']];
                db_execute($sql, $params);
                redirect('/admin/experience/', 'Experience created successfully!');
            }
        }
    }
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/experience/" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card" data-validate>
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" value="<?= e($formData['company_name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="position" value="<?= e($formData['position']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" value="<?= e($formData['location']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" value="<?= e($formData['start_date']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="<?= e($formData['end_date']) ?>" <?= $formData['is_current'] ? 'disabled' : '' ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_current" id="is_current" <?= $formData['is_current'] ? 'checked' : '' ?> onchange="toggleCurrent()">
                            <label class="form-check-label" for="is_current">Current Position</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Company Logo</label>
                    <div class="d-flex align-items-center gap-3">
                        <?php if ($formData['company_logo']): ?>
                            <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/experience/' . $formData['company_logo']) ?>" alt="" style="max-height: 60px;">
                        <?php else: ?>
                            <div class="bg-white border rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-building text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="company_logo" accept="image/*">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Company URL</label>
                    <input type="url" class="form-control" name="company_url" value="<?= e($formData['company_url']) ?>" placeholder="https://company.com">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"><?= e($formData['description']) ?></textarea>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Achievements</h6>
                        <div id="achievements_container">
                            <?php foreach ($formData['achievements'] as $ach): ?>
                                <div class="input-group mb-2 achievement-item">
                                    <input type="text" class="form-control" name="achievements[]" value="<?= e($ach) ?>" placeholder="e.g., Led team of 10 developers">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.achievement-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($formData['achievements'])): ?>
                                <div class="input-group mb-2 achievement-item">
                                    <input type="text" class="form-control" name="achievements[]" placeholder="e.g., Led team of 10 developers">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.achievement-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAchievement()">
                            <i class="fas fa-plus me-1"></i>Add Achievement
                        </button>
                    </div>
                    <div class="col-md-6">
                        <h6>Technologies Used</h6>
                        <div id="technologies_container">
                            <?php foreach ($formData['technologies'] as $tech): ?>
                                <div class="input-group mb-2 tech-item">
                                    <input type="text" class="form-control" name="technologies[]" value="<?= e($tech) ?>" placeholder="e.g., PHP, Laravel, Vue.js">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.tech-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($formData['technologies'])): ?>
                                <div class="input-group mb-2 tech-item">
                                    <input type="text" class="form-control" name="technologies[]" placeholder="e.g., PHP, Laravel, Vue.js">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.tech-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTechnology()">
                            <i class="fas fa-plus me-1"></i>Add Technology
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Settings</h6>
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= $formData['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="<?= $formData['sort_order'] ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Experience
        </button>
        <a href="/admin/experience/" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

<script>
function toggleCurrent() {
    const isCurrent = document.getElementById('is_current').checked;
    const endDate = document.querySelector('input[name="end_date"]');
    endDate.disabled = isCurrent;
    if (isCurrent) {
        endDate.value = '';
    }
}

function addAchievement() {
    const container = document.getElementById('achievements_container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 achievement-item';
    div.innerHTML = '<input type="text" class="form-control" name="achievements[]" placeholder="e.g., Led team of 10 developers"><button type="button" class="btn btn-outline-danger" onclick="this.closest(\'.achievement-item\').remove()"><i class="fas fa-trash"></i></button>';
    container.appendChild(div);
}

function addTechnology() {
    const container = document.getElementById('technologies_container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 tech-item';
    div.innerHTML = '<input type="text" class="form-control" name="technologies[]" placeholder="e.g., PHP, Laravel, Vue.js"><button type="button" class="btn btn-outline-danger" onclick="this.closest(\'.tech-item\').remove()"><i class="fas fa-trash"></i></button>';
    container.appendChild(div);
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
