<?php
/**
 * Admin Education Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$isEdit = isset($_GET['id']);
$education = null;

if ($isEdit) {
    $education = db_one('SELECT * FROM educations WHERE id = ?', [(int)$_GET['id']]);
    if (!$education) {
        redirect('/admin/education/', 'Education not found.', 'danger');
    }
    $pageTitle = 'Edit Education';
} else {
    $pageTitle = 'Add Education';
}

$currentPage = 'education';
$errors = [];
$formData = [
    'degree' => '',
    'institution' => '',
    'subject' => '',
    'location' => '',
    'start_year' => '',
    'end_year' => '',
    'is_current' => 0,
    'description' => '',
    'grade' => '',
    'institution_logo' => '',
    'institution_url' => '',
    'is_active' => 1,
    'sort_order' => 0,
];

if ($isEdit) {
    $formData = array_merge($formData, $education);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $formData = [
            'degree' => trim($_POST['degree'] ?? ''),
            'institution' => trim($_POST['institution'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'start_year' => (int)($_POST['start_year'] ?? 0),
            'end_year' => $_POST['is_current'] ? null : ((int)($_POST['end_year'] ?? 0)),
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? ''),
            'grade' => trim($_POST['grade'] ?? ''),
            'institution_logo' => '',
            'institution_url' => trim($_POST['institution_url'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        
        if (empty($formData['degree']) || empty($formData['institution']) || !$formData['start_year']) {
            $errors[] = 'Degree, institution, and start year are required.';
        }
        
        if (!$formData['is_current'] && !$formData['end_year']) {
            $errors[] = 'End year is required unless currently studying.';
        }
        
        // Handle logo upload
        if (!empty($_FILES['institution_logo']['name'])) {
            $result = upload_image($_FILES['institution_logo'], 'education');
            if ($result['success']) {
                if ($isEdit && $education['institution_logo']) {
                    delete_file('education/' . $education['institution_logo']);
                }
                $formData['institution_logo'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['institution_logo'] = $education['institution_logo'];
        }
        
        if (empty($errors)) {
            if ($isEdit) {
                $sql = 'UPDATE educations SET degree=?, institution=?, subject=?, location=?, start_year=?, end_year=?, is_current=?, description=?, grade=?, institution_logo=?, institution_url=?, is_active=?, sort_order=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['degree'], $formData['institution'], $formData['subject'], $formData['location'], $formData['start_year'], $formData['end_year'], $formData['is_current'], $formData['description'], $formData['grade'], $formData['institution_logo'], $formData['institution_url'], $formData['is_active'], $formData['sort_order'], $education['id']];
                db_execute($sql, $params);
                redirect('/admin/education/', 'Education updated successfully!');
            } else {
                $sql = 'INSERT INTO educations (degree, institution, subject, location, start_year, end_year, is_current, description, grade, institution_logo, institution_url, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['degree'], $formData['institution'], $formData['subject'], $formData['location'], $formData['start_year'], $formData['end_year'], $formData['is_current'], $formData['description'], $formData['grade'], $formData['institution_logo'], $formData['institution_url'], $formData['is_active'], $formData['sort_order']];
                db_execute($sql, $params);
                redirect('/admin/education/', 'Education created successfully!');
            }
        }
    }
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/education/" class="btn btn-secondary">
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
                            <label class="form-label">Degree <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="degree" value="<?= e($formData['degree']) ?>" required placeholder="e.g., Bachelor of Science in Computer Science">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Institution <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="institution" value="<?= e($formData['institution']) ?>" required placeholder="e.g., University of Technology">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Subject/Major</label>
                            <input type="text" class="form-control" name="subject" value="<?= e($formData['subject']) ?>" placeholder="e.g., Computer Science">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" value="<?= e($formData['location']) ?>" placeholder="e.g., New York, USA">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Start Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="start_year" value="<?= e($formData['start_year']) ?>" required min="1900" max="<?= date('Y') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">End Year</label>
                            <input type="number" class="form-control" name="end_year" value="<?= e($formData['end_year']) ?>" min="1900" max="<?= date('Y') + 5 ?>" <?= $formData['is_current'] ? 'disabled' : '' ?>>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_current" id="is_current" <?= $formData['is_current'] ? 'checked' : '' ?> onchange="toggleCurrent()">
                            <label class="form-check-label" for="is_current">Currently Studying</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Institution Logo</label>
                    <div class="d-flex align-items-center gap-3">
                        <?php if ($formData['institution_logo']): ?>
                            <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/education/' . $formData['institution_logo']) ?>" alt="" style="max-height: 60px;">
                        <?php else: ?>
                            <div class="bg-white border rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-university text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="institution_logo" accept="image/*">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Institution URL</label>
                    <input type="url" class="form-control" name="institution_url" value="<?= e($formData['institution_url']) ?>" placeholder="https://university.edu">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Grade/CGPA</label>
                    <input type="text" class="form-control" name="grade" value="<?= e($formData['grade']) ?>" placeholder="e.g., 3.8/4.0 or First Class">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"><?= e($formData['description']) ?></textarea>
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
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Education
        </button>
        <a href="/admin/education/" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

<script>
function toggleCurrent() {
    const isCurrent = document.getElementById('is_current').checked;
    const endYear = document.querySelector('input[name="end_year"]');
    endYear.disabled = isCurrent;
    if (isCurrent) {
        endYear.value = '';
    }
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
