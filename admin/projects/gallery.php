<?php
/**
 * Admin Project Gallery Management
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$id = (int)($_GET['id'] ?? 0);
$project = db_one('SELECT * FROM projects WHERE id = ?', [$id]);

if (!$project) {
    redirect('/Tamim/admin/projects/', 'Project not found.', 'danger');
}

$pageTitle = 'Gallery: ' . $project['title'];
$currentPage = 'projects';

$gallery = json_decode($project['gallery_images'] ?? '[]', true) ?? [];

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_gallery'])) {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        redirect('/Tamim/admin/projects/gallery.php?id=' . $id, 'Invalid CSRF token.', 'danger');
    }
    
    if (!empty($_FILES['gallery_images']['name'][0])) {
        $newGallery = $gallery;
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
                    $newGallery[] = $result['filename'];
                }
            }
        }
        
        $galleryJson = json_encode(array_values($newGallery));
        db_execute('UPDATE projects SET gallery_images = ?, updated_at = NOW() WHERE id = ?', [$galleryJson, $id]);
        redirect('/Tamim/admin/projects/gallery.php?id=' . $id, 'Gallery images uploaded successfully!');
    }
}

// Handle image deletion
if (isset($_GET['remove'])) {
    $removeFile = basename($_GET['remove']);
    $newGallery = array_filter($gallery, fn($img) => $img !== $removeFile);
    $galleryJson = json_encode(array_values($newGallery));
    db_execute('UPDATE projects SET gallery_images = ?, updated_at = NOW() WHERE id = ?', [$galleryJson, $id]);
    
    delete_file('projects/gallery/' . $removeFile);
    redirect('/Tamim/admin/projects/gallery.php?id=' . $id, 'Image removed successfully!');
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/Tamim/admin/projects/edit.php?id=<?= $id ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Edit
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upload Images</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Select Images</label>
                        <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple required>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple files. Max 5MB each.</div>
                    </div>
                    <button type="submit" name="upload_gallery" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Images
                    </button>
                </form>
            </div>
        </div>
        
        <?php if (!empty($gallery)): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Current Gallery (<?= count($gallery) ?> images)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="row g-3 m-3 gallery-grid">
                        <?php foreach ($gallery as $index => $img): ?>
                            <div class="col-md-3 col-6 position-relative">
                                <div class="ratio ratio-4x3">
                                    <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/gallery/' . $img) ?>" alt="" class="img-fluid rounded object-fit-cover h-100 w-100">
                                </div>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <a href="/Tamim/admin/projects/gallery.php?id=<?= $id ?>&remove=<?= urlencode($img) ?>" 
                                       class="btn btn-sm btn-danger rounded-circle" 
                                       onclick="return confirm('Delete this image?')"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <div class="position-absolute bottom-0 start-0 m-2">
                                    <span class="badge bg-dark"><?= $index + 1 ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card mt-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No gallery images yet</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Project Info</h5>
            </div>
            <div class="card-body">
                <p><strong>Title:</strong> <?= e($project['title']) ?></p>
                <p><strong>Category:</strong> <?= e($project['category_name'] ?? 'Uncategorized') ?></p>
                <p><strong>Gallery Images:</strong> <?= count($gallery) ?></p>
                
                <?php if ($project['cover_image']): ?>
                    <div class="mt-3">
                        <strong>Cover Image:</strong><br>
                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $project['cover_image']) ?>" alt="" class="img-fluid rounded mt-2" style="max-height: 150px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>