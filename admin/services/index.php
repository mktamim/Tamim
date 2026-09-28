<?php
/**
 * Admin Services List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Services';
$currentPage = 'services';

$services = db_all('SELECT * FROM services ORDER BY sort_order, title');

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/services/create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Service
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($services)): ?>
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No services yet</h5>
                <p class="text-muted">Add your first service to get started</p>
                <a href="/admin/services/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Add Service
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Icon</th>
                            <th>Title</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 100px;">Featured</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $index => $service): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($service['icon_type'] === 'image' && $service['icon_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/services/' . $service['icon_image']) ?>" alt="" style="width: 32px; height: 32px;" class="rounded">
                                    <?php else: ?>
                                        <i class="<?= e($service['icon_class'] ?? 'fas fa-briefcase') ?> fa-lg text-success"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($service['title']) ?></strong>
                                    <?php if ($service['short_description']): ?>
                                        <br><small class="text-muted"><?= e(mb_strimwidth($service['short_description'], 0, 60, '...')) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $service['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $service['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $service['is_featured'] ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                                        <?= $service['is_featured'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                                <td><?= $service['sort_order'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/services/edit.php?id=<?= $service['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $service['id'] ?>, '/admin/services/delete.php', 'Are you sure you want to delete this service?')"
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
