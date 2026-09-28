<?php
/**
 * Admin Testimonial Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$isEdit = isset($_GET['id']);
$testimonial = null;

if ($isEdit) {
    $testimonial = db_one('SELECT * FROM testimonials WHERE id = ?', [(int)$_GET['id']]);
    if (!$testimonial) {
        redirect('/admin/testimonials/', 'Testimonial not found.', 'danger');
    }
    $pageTitle = 'Edit Testimonial';
} else {
    $pageTitle = 'Add Testimonial';
}

$currentPage = 'testimonials';
$errors = [];
$formData = [
    'client_name' => '',
    'client_designation' => '',
    'client_company' => '',
    'client_image' => '',
    'review' => '',
    'rating' => 5,
    'project_name' => '',
    'is_active' => 1,
    'is_featured' => 0,
    'sort_order' => 0,
];

if ($isEdit) {
    $formData = array_merge($formData, $testimonial);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $formData = [
            'client_name' => trim($_POST['client_name'] ?? ''),
            'client_designation' => trim($_POST['client_designation'] ?? ''),
            'client_company' => trim($_POST['client_company'] ?? ''),
            'client_image' => '',
            'review' => trim($_POST['review'] ?? ''),
            'rating' => (int)($_POST['rating'] ?? 5),
            'project_name' => trim($_POST['project_name'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        
        if (empty($formData['client_name'])) {
            $errors[] = 'Client name is required.';
        }
        
        if (empty($formData['review'])) {
            $errors[] = 'Review text is required.';
        }
        
        if ($formData['rating'] < 1 || $formData['rating'] > 5) {
            $errors[] = 'Rating must be between 1 and 5.';
        }
        
        // Handle image upload
        if (!empty($_FILES['client_image']['name'])) {
            $result = upload_image($_FILES['client_image'], 'testimonials');
            if ($result['success']) {
                if ($isEdit && $testimonial['client_image']) {
                    delete_file('testimonials/' . $testimonial['client_image']);
                }
                $formData['client_image'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['client_image'] = $testimonial['client_image'];
        }
        
        if (empty($errors)) {
            if ($isEdit) {
                $sql = 'UPDATE testimonials SET client_name=?, client_designation=?, client_company=?, client_image=?, review=?, rating=?, project_name=?, is_active=?, is_featured=?, sort_order=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['client_name'], $formData['client_designation'], $formData['client_company'], $formData['client_image'], $formData['review'], $formData['rating'], $formData['project_name'], $formData['is_active'], $formData['is_featured'], $formData['sort_order'], $testimonial['id']];
                db_execute($sql, $params);
                redirect('/admin/testimonials/', 'Testimonial updated successfully!');
            } else {
                $sql = 'INSERT INTO testimonials (client_name, client_designation, client_company, client_image, review, rating, project_name, is_active, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['client_name'], $formData['client_designation'], $formData['client_company'], $formData['client_image'], $formData['review'], $formData['rating'], $formData['project_name'], $formData['is_active'], $formData['is_featured'], $formData['sort_order']];
                db_execute($sql, $params);
                redirect('/admin/testimonials/', 'Testimonial created successfully!');
            }
        }
    }
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/testimonials/" class="btn btn-secondary">
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
                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="client_name" value="<?= e($formData['client_name']) ?>" required placeholder="e.g., John Smith">
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" name="client_designation" value="<?= e($formData['client_designation']) ?>" placeholder="e.g., CEO, CTO">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <input type="text" class="form-control" name="client_company" value="<?= e($formData['client_company']) ?>" placeholder="e.g., TechCorp Inc.">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Project Name (Optional)</label>
                    <input type="text" class="form-control" name="project_name" value="<?= e($formData['project_name']) ?>" placeholder="Related project name">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Review <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="review" rows="5" required placeholder="Client testimonial text..."><?= e($formData['review']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Rating <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center gap-3">
                        <select class="form-select" name="rating" style="width: auto;">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= $formData['rating'] == $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $formData['rating'] ? 'text-warning' : 'text-muted' ?>" data-rating="<?= $i ?>" style="font-size: 1.5rem; cursor: pointer;"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Client Photo</h6>
                        <div class="text-center mb-3">
                            <?php if ($formData['client_image']): ?>
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/testimonials/' . $formData['client_image']) ?>" alt="" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 100px; height: 100px;">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control" name="client_image" accept="image/*" data-preview="client_preview">
                        <div class="form-text">Recommended: 200x200px. Max 5MB.</div>
                        <img id="client_preview" src="" alt="" class="rounded-circle mt-2 d-none" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                </div>
                
                <div class="card bg-light mt-3">
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
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Testimonial
        </button>
        <a href="/admin/testimonials/" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

<script>
// Star rating interaction
document.querySelectorAll('.rating-stars i').forEach(function(star) {
    star.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        document.querySelector('select[name="rating"]').value = rating;
        
        document.querySelectorAll('.rating-stars i').forEach(function(s, i) {
            s.classList.toggle('text-warning', i < rating);
            s.classList.toggle('text-muted', i >= rating);
        });
    });
});
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
