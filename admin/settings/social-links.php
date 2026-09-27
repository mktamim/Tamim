<?php
/**
 * Admin Social Links
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$pageTitle = 'Social Links';
$currentPage = 'settings';

$socialLinks = db_all('SELECT * FROM social_links ORDER BY sort_order');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        redirect('/Tamim/admin/settings/social-links/', 'Invalid CSRF token.', 'danger');
    }
    
    if (isset($_POST['add_social'])) {
        $platform = trim($_POST['platform'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $label = trim($_POST['label'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        if (empty($platform) || empty($url)) {
            redirect('/Tamim/admin/settings/social-links/', 'Platform and URL are required.', 'danger');
        }
        
        db_execute('INSERT INTO social_links (platform, icon_class, url, label, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)', [$platform, $icon_class, $url, $label, $is_active, $sort_order]);
        redirect('/Tamim/admin/settings/social-links/', 'Social link added successfully!');
    }
    
    if (isset($_POST['edit_social'])) {
        $id = (int)($_POST['id'] ?? 0);
        $platform = trim($_POST['platform'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $label = trim($_POST['label'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        if (empty($platform) || empty($url)) {
            redirect('/Tamim/admin/settings/social-links/', 'Platform and URL are required.', 'danger');
        }
        
        db_execute('UPDATE social_links SET platform=?, icon_class=?, url=?, label=?, is_active=?, sort_order=?, updated_at=NOW() WHERE id=?', [$platform, $icon_class, $url, $label, $is_active, $sort_order, $id]);
        redirect('/Tamim/admin/settings/social-links/', 'Social link updated successfully!');
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db_execute('DELETE FROM social_links WHERE id = ?', [$id]);
    redirect('/Tamim/admin/settings/social-links/', 'Social link deleted successfully!');
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) </h1>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add Social Link</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="add_social" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Platform <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="platform" required placeholder="e.g., facebook, twitter, linkedin">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">FontAwesome Icon Class <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="icon_class" required placeholder="e.g., fab fa-facebook-f">
                        <div class="form-text">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> for icons.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="url" required placeholder="https://facebook.com/username">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" name="label" placeholder="e.g., Follow on Facebook">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Social Link
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Social Links List</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($socialLinks)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-link fa-2x mb-2"></i>
                        <p>No social links yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="width: 60px;">Icon</th>
                                    <th>Platform</th>
                                    <th style="width: 200px;">URL</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 80px;">Order</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($socialLinks as $index => $social): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <i class="<?= e($social['icon_class'] ?? 'fas fa-link') ?> fa-lg text-primary"></i>
                                        </td>
                                        <td>
                                            <strong><?= e(ucfirst($social['platform'])) ?></strong>
                                            <?php if ($social['label']): ?>
                                                <br><small class="text-muted"><?= e($social['label']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><code><?= e($social['url']) ?></code></td>
                                        <td>
                                            <span class="badge <?= $social['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $social['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td><?= $social['sort_order'] ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editSocial(<?= htmlspecialchars(json_encode($social)) ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="/Tamim/admin/settings/social-links/?delete=<?= $social['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Delete this social link?')">
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
<div class="modal fade" id="editSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="edit_social" value="1">
                <input type="hidden" name="id" id="edit_social_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Social Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Platform <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="platform" id="edit_social_platform" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">FontAwesome Icon Class <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="icon_class" id="edit_social_icon" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="url" id="edit_social_url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" name="label" id="edit_social_label">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="is_active" id="edit_social_active">
                                <label class="form-check-label" for="edit_social_active">Active</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" id="edit_social_order" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSocial(social) {
    document.getElementById('edit_social_id').value = social.id;
    document.getElementById('edit_social_platform').value = social.platform;
    document.getElementById('edit_social_icon').value = social.icon_class;
    document.getElementById('edit_social_url').value = social.url;
    document.getElementById('edit_social_label').value = social.label || '';
    document.getElementById('edit_social_active').checked = social.is_active == 1;
    document.getElementById('edit_social_order').value = social.sort_order;
    
    new bootstrap.Modal(document.getElementById('editSocialModal')).show();
}
</script>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>