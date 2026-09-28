<?php
/**
 * Admin Blog Create/Edit
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$isEdit = isset($_GET['id']);
$post = null;
$categories = db_all('SELECT * FROM blog_categories WHERE is_active = 1 ORDER BY sort_order');

if ($isEdit) {
    $post = db_one('SELECT * FROM blog_posts WHERE id = ?', [(int)$_GET['id']]);
    if (!$post) {
        redirect('/admin/blog/', 'Post not found.', 'danger');
    }
    $pageTitle = 'Edit Post';
} else {
    $pageTitle = 'New Post';
}

$currentPage = 'blog';
$errors = [];
$formData = [
    'category_id' => '',
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'featured_image' => '',
    'tags' => [],
    'status' => 'draft',
    'is_featured' => 0,
    'meta_title' => '',
    'meta_description' => '',
    'meta_keywords' => '',
    'published_at' => null,
];

if ($isEdit) {
    $formData = array_merge($formData, $post);
    if ($post['tags']) {
        $formData['tags'] = json_decode($post['tags'], true) ?? [];
    }
    if ($post['published_at']) {
        $formData['published_at'] = date('Y-m-d\TH:i', strtotime($post['published_at']));
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
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'featured_image' => '',
            'tags' => array_filter(array_map('trim', explode(',', $_POST['tags'] ?? ''))),
            'status' => $_POST['status'] ?? 'draft',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
            'published_at' => !empty($_POST['published_at']) ? $_POST['published_at'] : null,
        ];
        
        if (empty($formData['title'])) {
            $errors[] = 'Post title is required.';
        }
        
        if (empty($formData['slug'])) {
            $formData['slug'] = slugify($formData['title']);
        }
        
        $existingSlug = db_one('SELECT id FROM blog_posts WHERE slug = ?' . ($isEdit ? ' AND id != ?' : ''), $isEdit ? [$formData['slug'], $post['id']] : [$formData['slug']]);
        if ($existingSlug) {
            $formData['slug'] = unique_slug('blog_posts', $formData['slug'], 'slug', $isEdit ? $post['id'] : null);
        }
        
        // Set published_at if publishing
        if ($formData['status'] === 'published' && !$formData['published_at']) {
            $formData['published_at'] = date('Y-m-d H:i:s');
        } elseif ($formData['status'] !== 'published') {
            $formData['published_at'] = null;
        }
        
        // Handle featured image upload
        if (!empty($_FILES['featured_image']['name'])) {
            $result = upload_image($_FILES['featured_image'], 'blog');
            if ($result['success']) {
                if ($isEdit && $post['featured_image']) {
                    delete_file('blog/' . $post['featured_image']);
                }
                $formData['featured_image'] = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        } elseif ($isEdit) {
            $formData['featured_image'] = $post['featured_image'];
        }
        
        if (empty($errors)) {
            $tagsJson = json_encode(array_values($formData['tags']));
            
            if ($isEdit) {
                $sql = 'UPDATE blog_posts SET category_id=?, title=?, slug=?, excerpt=?, content=?, featured_image=?, tags=?, status=?, is_featured=?, meta_title=?, meta_description=?, meta_keywords=?, published_at=?, updated_at=NOW() WHERE id=?';
                $params = [$formData['category_id'], $formData['title'], $formData['slug'], $formData['excerpt'], $formData['content'], $formData['featured_image'], $tagsJson, $formData['status'], $formData['is_featured'], $formData['meta_title'], $formData['meta_description'], $formData['meta_keywords'], $formData['published_at'], $post['id']];
                db_execute($sql, $params);
                redirect('/admin/blog/', 'Post updated successfully!');
            } else {
                $sql = 'INSERT INTO blog_posts (category_id, title, slug, excerpt, content, featured_image, tags, status, is_featured, meta_title, meta_description, meta_keywords, published_at, author_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $params = [$formData['category_id'], $formData['title'], $formData['slug'], $formData['excerpt'], $formData['content'], $formData['featured_image'], $tagsJson, $formData['status'], $formData['is_featured'], $formData['meta_title'], $formData['meta_description'], $formData['meta_keywords'], $formData['published_at'], $_SESSION['admin_id']];
                db_execute($sql, $params);
                redirect('/admin/blog/', 'Post created successfully!');
            }
        }
    }
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/blog/" class="btn btn-secondary">
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
                    <label class="form-label">Post Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="<?= e($formData['title']) ?>" required placeholder="Enter post title">
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
                    <label class="form-label">Excerpt</label>
                    <textarea class="form-control" name="excerpt" rows="3" placeholder="Brief summary for post cards and SEO"><?= e($formData['excerpt']) ?></textarea>
                    <div class="form-text">Shown in blog listings and as meta description if empty.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea class="form-control" name="content" rows="15" placeholder="Write your blog post content here..."><?= e($formData['content']) ?></textarea>
                    <div class="form-text">Supports HTML. You can use basic formatting tags.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tags (comma separated)</label>
                    <input type="text" class="form-control" name="tags" value="<?= e(implode(', ', $formData['tags'])) ?>" placeholder="php, laravel, web development, tutorial">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Featured Image</h6>
                        <div class="mb-3">
                            <?php if ($formData['featured_image']): ?>
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/blog/' . $formData['featured_image']) ?>" alt="" class="img-fluid rounded mb-2" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="bg-white rounded d-flex align-items-center justify-content-center mb-2" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="featured_image" accept="image/*" data-preview="featured_preview">
                            <div class="form-text">Recommended: 1200x630px. Max 5MB.</div>
                        </div>
                        <img id="featured_preview" src="" alt="" class="img-fluid rounded d-none" style="max-height: 200px;">
                    </div>
                </div>
                
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Publish Settings</h6>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status_select" onchange="togglePublishedAt()">
                                <option value="draft" <?= $formData['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $formData['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= $formData['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="published_at_group" style="display: <?= $formData['status'] === 'published' ? 'block' : 'none' ?>;">
                            <label class="form-label">Published At</label>
                            <input type="datetime-local" class="form-control" name="published_at" value="<?= e($formData['published_at']) ?>">
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" <?= $formData['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Featured Post</label>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title">SEO Settings</h6>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="<?= e($formData['meta_title']) ?>" placeholder="Defaults to post title" maxlength="60">
                            <div class="form-text">Max 60 characters for best SEO.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="3" placeholder="Defaults to excerpt" maxlength="160"><?= e($formData['meta_description']) ?></textarea>
                            <div class="form-text">Max 160 characters for best SEO.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" value="<?= e($formData['meta_keywords']) ?>" placeholder="keyword1, keyword2, keyword3">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Post
        </button>
        <?php if ($isEdit && $formData['status'] === 'published'): ?>
            <a href="/blog/<?= e($formData['slug']) ?>" target="_blank" class="btn btn-info ms-2">
                <i class="fas fa-external-link-alt me-2"></i>View Live
            </a>
        <?php endif; ?>
        <a href="/admin/blog/" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

<script>
function togglePublishedAt() {
    const status = document.getElementById('status_select').value;
    const group = document.getElementById('published_at_group');
    group.style.display = status === 'published' ? 'block' : 'none';
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
