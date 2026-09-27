<?php
/**
 * Admin Skill Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$isEdit = isset($_GET['id']);
$skill = null;

if ($isEdit) {
    $skill = db_one('SELECT * FROM skills WHERE id = ?', [(int)$_GET['id']]);
    if (!$skill) {
        redirect('/Tamim/admin/skills/', 'Skill not found.', 'danger');
    }
    $pageTitle = 'Edit Skill';
} else {
    $pageTitle = 'Add Skill';
}

$currentPage = 'skills';
$errors = [];
$formData = [
    'name' => '',
    'icon_class' => '',
    'icon_type' => 'fontawesome',
    'icon_image' => '',
    'percentage' => 80,
    'description' => '',
    'category' => 'technical',
    'is_active' => 1,
    'sort_order' => 0,
];

if ($isEdit) {
    $formData = array_merge($formData, $skill);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $formData = [
            'name' => trim($_POST['name'] ?? ''),
            'icon_class' => trim($_POST['icon_class'] ?? ''),
            'icon_type' => $_POST['icon_type'] ?? 'fontawesome',
            'icon_image' => '',
            'percentage' => (int)($_POST['percentage'] ?? 80),
            'description' => trim($_POST['description'] ?? ''),
            'category' => $_POST['category'] ?? 'technical',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        
        if (empty($formData['name'])) {
            $errors[] = 'Skill name is required.';
        }
        
        if ($formData['percentage'] < 0 || $formData['percentage'] > 100) {
            $errors[] = 'Percentage must be between 0 and 100.';
        }
        
        // Handle icon image upload
        if ($formData['icon_type'] === 'image' && !empty($_FILES['icon_image']['name'])) {
            $result = upload_image($_FILES['icon_image'], 'skills');
            if ($result['success']) {
                // Delete old image if exists
                if ($isEdit && $skill['icon_image']) {
                    delete_file('skills/' . $skill['icon_image']);
                }
                $formData['icon_image'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['icon_image'] = $skill['icon_image'];
        }
        
        if (empty($errors)) {
            if ($isEdit) {
                $sql = 'UPDATE skills SET name=?, icon_class=?, icon_type=?, icon_image=?, percentage=?, description=?, category=?, is_active=?, sort_order=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['name'], $formData['icon_class'], $formData['icon_type'], $formData['icon_image'], $formData['percentage'], $formData['description'], $formData['category'], $formData['is_active'], $formData['sort_order'], $skill['id']];
                db_execute($sql, $params);
                redirect('/Tamim/admin/skills/', 'Skill updated successfully!');
            } else {
                $sql = 'INSERT INTO skills (name, icon_class, icon_type, icon_image, percentage, description, category, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['name'], $formData['icon_class'], $formData['icon_type'], $formData['icon_image'], $formData['percentage'], $formData['description'], $formData['category'], $formData['is_active'], $formData['sort_order']];
                db_execute($sql, $params);
                redirect('/Tamim/admin/skills/', 'Skill created successfully!');
            }
        }
    }
}

$categories = ['technical' => 'Technical', 'frontend' => 'Frontend', 'backend' => 'Backend', 'tools' => 'Tools', 'cms' => 'CMS', 'other' => 'Other'];

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/Tamim/admin/skills/" class="btn btn-secondary">
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
                <div class="mb-3">
                    <label class="form-label">Skill Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="<?= e($formData['name']) ?>" required placeholder="e.g., PHP, Laravel, JavaScript">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Brief description of this skill"><?= e($formData['description']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Icon Type</label>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="icon_type" id="icon_fa" value="fontawesome" <?= $formData['icon_type'] === 'fontawesome' ? 'checked' : '' ?> onchange="toggleIconType()">
                        <label class="btn btn-outline-primary" for="icon_fa"><i class="fab fa-font-awesome me-1"></i>FontAwesome</label>
                        
                        <input type="radio" class="btn-check" name="icon_type" id="icon_img" value="image" <?= $formData['icon_type'] === 'image' ? 'checked' : '' ?> onchange="toggleIconType()">
                        <label class="btn btn-outline-primary" for="icon_img"><i class="fas fa-image me-1"></i>Upload Image</label>
                    </div>
                </div>
                
                <div class="mb-3" id="icon_fa_group" style="display: <?= $formData['icon_type'] === 'fontawesome' ? 'block' : 'none' ?>;">
                    <label class="form-label">FontAwesome Icon Class</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-code"></i></span>
                        <input type="text" class="form-control" name="icon_class" value="<?= e($formData['icon_class']) ?>" placeholder="e.g., fab fa-php, fas fa-code">
                    </div>
                    <div class="form-text">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome Icons</a> to find icon classes.</div>
                </div>
                
                <div class="mb-3" id="icon_img_group" style="display: <?= $formData['icon_type'] === 'image' ? 'block' : 'none' ?>;">
                    <label class="form-label">Upload Icon Image</label>
                    <input type="file" class="form-control" name="icon_image" accept="image/*" data-preview="icon_preview">
                    <div class="form-text">Upload SVG, PNG, or WebP. Recommended size: 64x64px. Max 5MB.</div>
                    <?php if ($formData['icon_image']): ?>
                        <div class="mt-2">
                            <img id="icon_preview" src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/skills/' . $formData['icon_image']) ?>" alt="" style="max-width: 64px; max-height: 64px;" class="rounded border">
                        </div>
                    <?php else: ?>
                        <img id="icon_preview" src="" alt="" style="max-width: 64px; max-height: 64px; display: none;" class="rounded border mt-2">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Settings</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <?php foreach ($categories as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $formData['category'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Proficiency Level (%)</label>
                            <div class="input-group">
                                <input type="range" class="form-range" name="percentage" min="0" max="100" value="<?= $formData['percentage'] ?>" id="percentage_range" oninput="document.getElementById('percentage_value').value = this.value">
                                <input type="number" class="form-control" name="percentage" id="percentage_value" min="0" max="100" value="<?= $formData['percentage'] ?>" oninput="document.getElementById('percentage_range').value = this.value" style="width: 70px;">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="<?= $formData['sort_order'] ?>" min="0">
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= $formData['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Skill
        </button>
        <a href="/Tamim/admin/skills/" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

<script>
function toggleIconType() {
    const fa = document.getElementById('icon_fa_group');
    const img = document.getElementById('icon_img_group');
    const type = document.querySelector('input[name="icon_type"]:checked').value;
    
    fa.style.display = type === 'fontawesome' ? 'block' : 'none';
    img.style.display = type === 'image' ? 'block' : 'none';
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>