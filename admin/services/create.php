<?php
/**
 * Admin Service Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$isEdit = isset($_GET['id']);
$service = null;

if ($isEdit) {
    $service = db_one('SELECT * FROM services WHERE id = ?', [(int)$_GET['id']]);
    if (!$service) {
        redirect('/admin/services/', 'Service not found.', 'danger');
    }
    $pageTitle = 'Edit Service';
} else {
    $pageTitle = 'Add Service';
}

$currentPage = 'services';
$errors = [];
$formData = [
    'title' => '',
    'slug' => '',
    'short_description' => '',
    'full_description' => '',
    'icon_class' => '',
    'icon_type' => 'fontawesome',
    'icon_image' => '',
    'features' => [],
    'is_active' => 1,
    'is_featured' => 0,
    'sort_order' => 0,
];

if ($isEdit) {
    $formData = array_merge($formData, $service);
    if ($service['features']) {
        $formData['features'] = json_decode($service['features'], true) ?? [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $formData = [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'full_description' => trim($_POST['full_description'] ?? ''),
            'icon_class' => trim($_POST['icon_class'] ?? ''),
            'icon_type' => $_POST['icon_type'] ?? 'fontawesome',
            'icon_image' => '',
            'features' => array_filter(array_map('trim', $_POST['features'] ?? [])),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        
        if (empty($formData['title'])) {
            $errors[] = 'Service title is required.';
        }
        
        if (empty($formData['slug'])) {
            $formData['slug'] = slugify($formData['title']);
        }
        
        // Check slug uniqueness
        $existingSlug = db_one('SELECT id FROM services WHERE slug = ?' . ($isEdit ? ' AND id != ?' : ''), $isEdit ? [$formData['slug'], $service['id']] : [$formData['slug']]);
        if ($existingSlug) {
            $formData['slug'] = unique_slug('services', $formData['slug'], 'slug', $isEdit ? $service['id'] : null);
        }
        
        // Handle icon image upload
        if ($formData['icon_type'] === 'image' && !empty($_FILES['icon_image']['name'])) {
            $result = upload_image($_FILES['icon_image'], 'services');
            if ($result['success']) {
                if ($isEdit && $service['icon_image']) {
                    delete_file('services/' . $service['icon_image']);
                }
                $formData['icon_image'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['icon_image'] = $service['icon_image'];
        }
        
        if (empty($errors)) {
            $featuresJson = json_encode(array_values($formData['features']));
            
            if ($isEdit) {
                $sql = 'UPDATE services SET title=?, slug=?, short_description=?, full_description=?, icon_class=?, icon_type=?, icon_image=?, features=?, is_active=?, is_featured=?, sort_order=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['title'], $formData['slug'], $formData['short_description'], $formData['full_description'], $formData['icon_class'], $formData['icon_type'], $formData['icon_image'], $featuresJson, $formData['is_active'], $formData['is_featured'], $formData['sort_order'], $service['id']];
                db_execute($sql, $params);
                redirect('/admin/services/', 'Service updated successfully!');
            } else {
                $sql = 'INSERT INTO services (title, slug, short_description, full_description, icon_class, icon_type, icon_image, features, is_active, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['title'], $formData['slug'], $formData['short_description'], $formData['full_description'], $formData['icon_class'], $formData['icon_type'], $formData['icon_image'], $featuresJson, $formData['is_active'], $formData['is_featured'], $formData['sort_order']];
                db_execute($sql, $params);
                redirect('/admin/services/', 'Service created successfully!');
            }
        }
    }
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/services/" class="btn btn-secondary">
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
                    <label class="form-label">Service Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="<?= e($formData['title']) ?>" required placeholder="e.g., Web Development">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" class="form-control" name="slug" value="<?= e($formData['slug']) ?>" placeholder="auto-generated from title">
                    <div class="form-text">Leave empty to auto-generate. Used in URLs.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <textarea class="form-control" name="short_description" rows="2" placeholder="Brief description for cards"><?= e($formData['short_description']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea class="form-control" name="full_description" rows="5" placeholder="Detailed description for service page"><?= e($formData['full_description']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Features (one per line)</label>
                    <textarea class="form-control" name="features[]" rows="6" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"><?= e(implode("\n", $formData['features'])) ?></textarea>
                    <div class="form-text">Each line becomes a feature item.</div>
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
                        <input type="text" class="form-control" name="icon_class" value="<?= e($formData['icon_class']) ?>" placeholder="e.g., fas fa-code, fas fa-briefcase">
                    </div>
                </div>
                
                <div class="mb-3" id="icon_img_group" style="display: <?= $formData['icon_type'] === 'image' ? 'block' : 'none' ?>;">
                    <label class="form-label">Upload Icon Image</label>
                    <input type="file" class="form-control" name="icon_image" accept="image/*" data-preview="icon_preview">
                    <div class="form-text">Upload SVG, PNG, or WebP. Recommended size: 64x64px. Max 5MB.</div>
                    <?php if ($formData['icon_image']): ?>
                        <div class="mt-2">
                            <img id="icon_preview" src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/services/' . $formData['icon_image']) ?>" alt="" style="max-width: 64px; max-height: 64px;" class="rounded border">
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
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= $formData['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" <?= $formData['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Featured</label>
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
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Service
        </button>
        <a href="/admin/services/" class="btn btn-secondary ms-2">Cancel</a>
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
