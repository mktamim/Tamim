<?php
/**
 * Admin Experiences List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Experience';
$currentPage = 'experience';

$experiences = db_all('SELECT * FROM experiences ORDER BY start_date DESC');

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/experience/create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Experience
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($experiences)): ?>
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No experience entries yet</h5>
                <a href="/admin/experience/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Add Experience
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Position</th>
                            <th style="width: 150px;">Company</th>
                            <th style="width: 150px;">Duration</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experiences as $index => $exp): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= e($exp['position']) ?></strong>
                                    <?php if ($exp['company_logo']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/experience/' . $exp['company_logo']) ?>" alt="" style="width: 32px; height: 32px; object-fit: contain;" class="rounded ms-2">
                                    <?php endif; ?>
                                </td>
                                <td><?= e($exp['company_name']) ?></td>
                                <td>
                                    <?= format_date($exp['start_date'], 'M Y') ?> - 
                                    <?= $exp['is_current'] ? 'Present' : format_date($exp['end_date'], 'M Y') ?>
                                </td>
                                <td>
                                    <span class="badge <?= $exp['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $exp['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= $exp['sort_order'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/experience/edit.php?id=<?= $exp['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $exp['id'] ?>, '/admin/experience/delete.php', 'Are you sure you want to delete this experience?')"
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
