<?php
/**
 * Admin Blog Categories
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Blog Categories';
$currentPage = 'blog';

$categories = db_all('SELECT * FROM blog_categories ORDER BY sort_order, name');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        redirect('/admin/blog/categories/', 'Invalid CSRF token.', 'danger');
    }
    
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    
    if (empty($name)) {
        redirect('/admin/blog/categories/', 'Category name is required.', 'danger');
    }
    
    if (empty($slug)) {
        $slug = slugify($name);
    }
    
    $existing = db_one('SELECT id FROM blog_categories WHERE slug = ?' . ($edit_id ? ' AND id != ?' : ''), $edit_id ? [$slug, $edit_id] : [$slug]);
    if ($existing) {
        $slug = unique_slug('blog_categories', $slug, 'slug', $edit_id);
    }
    
    if ($edit_id) {
        db_execute('UPDATE blog_categories SET name=?, slug=?, description=?, is_active=?, sort_order=?, updated_at=NOW() WHERE id=?', [$name, $slug, $description, $is_active, $sort_order, $edit_id]);
        redirect('/admin/blog/categories/', 'Category updated successfully!');
    } else {
        db_execute('INSERT INTO blog_categories (name, slug, description, is_active, sort_order) VALUES (?, ?, ?, ?, ?)', [$name, $slug, $description, $is_active, $sort_order]);
        redirect('/admin/blog/categories/', 'Category created successfully!');
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db_execute('DELETE FROM blog_categories WHERE id = ?', [$id]);
    redirect('/admin/blog/categories/', 'Category deleted successfully!');
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add Category</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="edit_id" value="0">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., Web Development">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" placeholder="auto-generated">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0" min="0">
                    </div>
                    <button type="submit" name="save_category" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Categories List</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder fa-2x mb-2"></i>
                        <p>No categories yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Name</th>
                                    <th style="width: 150px;">Slug</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 80px;">Order</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $index => $cat): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= e($cat['name']) ?></strong></td>
                                        <td><code><?= e($cat['slug']) ?></code></td>
                                        <td>
                                            <span class="badge <?= $cat['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td><?= $cat['sort_order'] ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="/admin/blog/categories/?delete=<?= $cat['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Delete this category?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" id="edit_slug">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="is_active" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="edit_sort_order" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_category" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('edit_id').value = cat.id;
    document.getElementById('edit_name').value = cat.name;
    document.getElementById('edit_slug').value = cat.slug;
    document.getElementById('edit_description').value = cat.description || '';
    document.getElementById('edit_is_active').checked = cat.is_active == 1;
    document.getElementById('edit_sort_order').value = cat.sort_order;
    
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
