<?php
/**
 * Admin Projects List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$pageTitle = 'Projects';
$currentPage = 'projects';

$projects = db_all('SELECT p.*, pc.name as category_name FROM projects p LEFT JOIN project_categories pc ON p.category_id = pc.id ORDER BY p.sort_order, p.created_at DESC');

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <div class="d-flex gap-2">
        <a href="/Tamim/admin/project_categories/" class="btn btn-outline-secondary">
            <i class="fas fa-tags me-2"></i>Categories
        </a>
        <a href="/Tamim/admin/projects/create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Project
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($projects)): ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No projects yet</h5>
                <p class="text-muted">Add your first project to showcase your work</p>
                <a href="/Tamim/admin/projects/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Add Project
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 80px;">Cover</th>
                            <th>Title</th>
                            <th style="width: 120px;">Category</th>
                            <th style="width: 100px;">Featured</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $index => $project): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($project['cover_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $project['cover_image']) ?>" alt="" style="width: 60px; height: 40px; object-fit: cover;" class="rounded">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($project['title']) ?></strong>
                                    <?php if ($project['short_description']): ?>
                                        <br><small class="text-muted"><?= e(mb_strimwidth($project['short_description'], 0, 60, '...')) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= e($project['category_name'] ?? 'Uncategorized') ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= $project['is_featured'] ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                                        <?= $project['is_featured'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $project['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $project['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= $project['sort_order'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/Tamim/admin/projects/edit.php?id=<?= $project['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/Tamim/admin/projects/gallery.php?id=<?= $project['id'] ?>" class="btn btn-outline-info" title="Gallery">
                                            <i class="fas fa-images"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $project['id'] ?>, '/Tamim/admin/projects/delete.php', 'Are you sure you want to delete this project?')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>