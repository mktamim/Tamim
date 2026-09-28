<?php
/**
 * Admin Project Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$isEdit = isset($_GET['id']);
$project = null;
$categories = db_all('SELECT * FROM project_categories WHERE is_active = 1 ORDER BY sort_order');

if ($isEdit) {
    $project = db_one('SELECT * FROM projects WHERE id = ?', [(int)$_GET['id']]);
    if (!$project) {
        redirect('/admin/projects/', 'Project not found.', 'danger');
    }
    $pageTitle = 'Edit Project';
} else {
    $pageTitle = 'Add Project';
}

$currentPage = 'projects';
$errors = [];
$formData = [
    'category_id' => '',
    'title' => '',
    'slug' => '',
    'short_description' => '',
    'full_description' => '',
    'cover_image' => '',
    'gallery_images' => [],
    'technologies' => [],
    'features' => [],
    'challenges' => '',
    'solution' => '',
    'live_url' => '',
    'github_url' => '',
    'client_name' => '',
    'project_date' => '',
    'is_featured' => 0,
    'is_active' => 1,
    'sort_order' => 0,
];

if ($isEdit) {
    $formData = array_merge($formData, $project);
    if ($project['gallery_images']) {
        $formData['gallery_images'] = json_decode($project['gallery_images'], true) ?? [];
    }
    if ($project['technologies']) {
        $formData['technologies'] = json_decode($project['technologies'], true) ?? [];
    }
    if ($project['features']) {
        $formData['features'] = json_decode($project['features'], true) ?? [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $formData = [
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'title' => trim($_POST['title'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'full_description' => trim($_POST['full_description'] ?? ''),
            'cover_image' => '',
            'gallery_images' => [],
            'technologies' => array_filter(array_map('trim', $_POST['technologies'] ?? [])),
            'features' => array_filter(array_map('trim', $_POST['features'] ?? [])),
            'challenges' => trim($_POST['challenges'] ?? ''),
            'solution' => trim($_POST['solution'] ?? ''),
            'live_url' => trim($_POST['live_url'] ?? ''),
            'github_url' => trim($_POST['github_url'] ?? ''),
            'client_name' => trim($_POST['client_name'] ?? ''),
            'project_date' => !empty($_POST['project_date']) ? $_POST['project_date'] : null,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        
        if (empty($formData['title'])) {
            $errors[] = 'Project title is required.';
        }
        
        if (empty($formData['slug'])) {
            $formData['slug'] = slugify($formData['title']);
        }
        
        $existingSlug = db_one('SELECT id FROM projects WHERE slug = ?' . ($isEdit ? ' AND id != ?' : ''), $isEdit ? [$formData['slug'], $project['id']] : [$formData['slug']]);
        if ($existingSlug) {
            $formData['slug'] = unique_slug('projects', $formData['slug'], 'slug', $isEdit ? $project['id'] : null);
        }
        
        // Handle cover image upload
        if (!empty($_FILES['cover_image']['name'])) {
            $result = upload_image($_FILES['cover_image'], 'projects');
            if ($result['success']) {
                if ($isEdit && $project['cover_image']) {
                    delete_file('projects/' . $project['cover_image']);
                }
                $formData['cover_image'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['cover_image'] = $project['cover_image'];
        }
        
        // Handle gallery images
        if (!empty($_FILES['gallery_images']['name'][0])) {
            $gallery = [];
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['gallery_images']['name'][$key],
                        'type' => $_FILES['gallery_images']['type'][$key],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['gallery_images']['error'][$key],
                        'size' => $_FILES['gallery_images']['size'][$key],
                    ];
                    $result = upload_image($file, 'projects/gallery');
                    if ($result['success']) {
                        $gallery[] = $result['filename'];
                    }
                }
            }
            // Keep existing gallery images if any
            if ($isEdit && $project['gallery_images']) {
                $existingGallery = json_decode($project['gallery_images'], true) ?? [];
                $gallery = array_merge($existingGallery, $gallery);
            }
            $formData['gallery_images'] = $gallery;
        } elseif ($isEdit) {
            $formData['gallery_images'] = json_decode($project['gallery_images'], true) ?? [];
        }
        
        if (empty($errors)) {
            $galleryJson = json_encode(array_values($formData['gallery_images']));
            $techJson = json_encode(array_values($formData['technologies']));
            $featuresJson = json_encode(array_values($formData['features']));
            
            if ($isEdit) {
                $sql = 'UPDATE projects SET category_id=?, title=?, slug=?, short_description=?, full_description=?, cover_image=?, gallery_images=?, technologies=?, features=?, challenges=?, solution=?, live_url=?, github_url=?, client_name=?, project_date=?, is_featured=?, is_active=?, sort_order=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['category_id'], $formData['title'], $formData['slug'], $formData['short_description'], $formData['full_description'], $formData['cover_image'], $galleryJson, $techJson, $featuresJson, $formData['challenges'], $formData['solution'], $formData['live_url'], $formData['github_url'], $formData['client_name'], $formData['project_date'], $formData['is_featured'], $formData['is_active'], $formData['sort_order'], $project['id']];
                db_execute($sql, $params);
                redirect('/admin/projects/', 'Project updated successfully!');
            } else {
                $sql = 'INSERT INTO projects (category_id, title, slug, short_description, full_description, cover_image, gallery_images, technologies, features, challenges, solution, live_url, github_url, client_name, project_date, is_featured, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['category_id'], $formData['title'], $formData['slug'], $formData['short_description'], $formData['full_description'], $formData['cover_image'], $galleryJson, $techJson, $featuresJson, $formData['challenges'], $formData['solution'], $formData['live_url'], $formData['github_url'], $formData['client_name'], $formData['project_date'], $formData['is_featured'], $formData['is_active'], $formData['sort_order']];
                db_execute($sql, $params);
                redirect('/admin/projects/', 'Project created successfully!');
            }
        }
    }
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/projects/" class="btn btn-secondary">
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
        <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" role="tab">Basic Info</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" role="tab">Content</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" role="tab">Media</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" role="tab">Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" role="tab">Settings</button>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Basic Info Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="<?= e($formData['title']) ?>" required placeholder="e.g., E-commerce Platform">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Slug (URL)</label>
                            <input type="text" class="form-control" name="slug" value="<?= e($formData['slug']) ?>" placeholder="auto-generated from title">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $formData['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea class="form-control" name="short_description" rows="2" placeholder="Brief summary for project cards"><?= e($formData['short_description']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control" name="full_description" rows="6" placeholder="Detailed project description"><?= e($formData['full_description']) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Cover Image</h6>
                                <div class="mb-3">
                                    <?php if ($formData['cover_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $formData['cover_image']) ?>" alt="" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                    <?php else: ?>
                                        <div class="bg-white rounded d-flex align-items-center justify-content-center mb-2" style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="cover_image" accept="image/*" data-preview="cover_preview">
                                    <div class="form-text">Recommended: 1200x800px. Max 5MB.</div>
                                </div>
                                <img id="cover_preview" src="" alt="" class="img-fluid rounded d-none" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Tab -->
            <div class="tab-pane fade" id="content" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6>Technologies Used</h6>
                        <div id="technologies_container">
                            <?php foreach ($formData['technologies'] as $index => $tech): ?>
                                <div class="input-group mb-2 technology-item">
                                    <input type="text" class="form-control" name="technologies[]" value="<?= e($tech) ?>" placeholder="e.g., PHP, Laravel, MySQL">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.technology-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($formData['technologies'])): ?>
                                <div class="input-group mb-2 technology-item">
                                    <input type="text" class="form-control" name="technologies[]" placeholder="e.g., PHP, Laravel, MySQL">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.technology-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTechnology()">
                            <i class="fas fa-plus me-1"></i>Add Technology
                        </button>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Key Features</h6>
                        <div id="features_container">
                            <?php foreach ($formData['features'] as $index => $feature): ?>
                                <div class="input-group mb-2 feature-item">
                                    <input type="text" class="form-control" name="features[]" value="<?= e($feature) ?>" placeholder="e.g., User authentication, Payment integration">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.feature-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($formData['features'])): ?>
                                <div class="input-group mb-2 feature-item">
                                    <input type="text" class="form-control" name="features[]" placeholder="e.g., User authentication, Payment integration">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.feature-item').remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFeature()">
                            <i class="fas fa-plus me-1"></i>Add Feature
                        </button>
                    </div>
                    
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Challenges Faced</label>
                                    <textarea class="form-control" name="challenges" rows="4"><?= e($formData['challenges']) ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Solution Provided</label>
                                    <textarea class="form-control" name="solution" rows="4"><?= e($formData['solution']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Media Tab -->
            <div class="tab-pane fade" id="media" role="tabpanel">
                <h6>Gallery Images</h6>
                <div class="mb-3">
                    <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                    <div class="form-text">Select multiple images (hold Ctrl/Cmd). Max 5MB each.</div>
                </div>
                
                <?php if (!empty($formData['gallery_images'])): ?>
                    <div class="row g-3 gallery-preview">
                        <?php foreach ($formData['gallery_images'] as $index => $img): ?>
                            <div class="col-md-3 col-6 position-relative">
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/gallery/' . $img) ?>" alt="" class="img-fluid rounded">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                        onclick="removeGalleryImage(this, '<?= e($img) ?>')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Details Tab -->
            <div class="tab-pane fade" id="details" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Live Demo URL</label>
                            <input type="url" class="form-control" name="live_url" value="<?= e($formData['live_url']) ?>" placeholder="https://example.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">GitHub URL</label>
                            <input type="url" class="form-control" name="github_url" value="<?= e($formData['github_url']) ?>" placeholder="https://github.com/username/repo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" name="client_name" value="<?= e($formData['client_name']) ?>" placeholder="Client or company name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Project Date</label>
                            <input type="date" class="form-control" name="project_date" value="<?= e($formData['project_date']) ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Visibility</h6>
                                <div class="mb-3 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= $formData['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">Active (visible on website)</label>
                                </div>
                                <div class="mb-3 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" <?= $formData['is_featured'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_featured">Featured (show on homepage)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Display Order</h6>
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" name="sort_order" value="<?= $formData['sort_order'] ?>" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Project
        </button>
        <a href="/admin/projects/" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

<script>
function addTechnology() {
    const container = document.getElementById('technologies_container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 technology-item';
    div.innerHTML = '<input type="text" class="form-control" name="technologies[]" placeholder="e.g., PHP, Laravel, MySQL"><button type="button" class="btn btn-outline-danger" onclick="this.closest(\'.technology-item\').remove()"><i class="fas fa-trash"></i></button>';
    container.appendChild(div);
}

function addFeature() {
    const container = document.getElementById('features_container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 feature-item';
    div.innerHTML = '<input type="text" class="form-control" name="features[]" placeholder="e.g., User authentication, Payment integration"><button type="button" class="btn btn-outline-danger" onclick="this.closest(\'.feature-item\').remove()"><i class="fas fa-trash"></i></button>';
    container.appendChild(div);
}

function removeGalleryImage(btn, filename) {
    if (confirm('Remove this gallery image?')) {
        btn.closest('.col-md-3').remove();
        // Add hidden input to track removed images
        const form = btn.closest('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'removed_gallery[]';
        input.value = filename;
        form.appendChild(input);
    }
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
