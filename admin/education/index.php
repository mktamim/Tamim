<?php
/**
 * Admin Education List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$pageTitle = 'Education';
$currentPage = 'education';

$educations = db_all('SELECT * FROM educations ORDER BY start_year DESC');

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/Tamim/admin/education/create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Education
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($educations)): ?>
            <div class="text-center py-5">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No education entries yet</h5>
                <a href="/Tamim/admin/education/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Add Education
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Degree</th>
                            <th style="width: 150px;">Institution</th>
                            <th style="width: 120px;">Years</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($educations as $index => $edu): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= e($edu['degree']) ?></strong>
                                    <?php if ($edu['subject']): ?>
                                        <br><small class="text-muted"><?= e($edu['subject']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($edu['institution']) ?></td>
                                <td>
                                    <?= $edu['start_year'] ?> - 
                                    <?= $edu['is_current'] ? 'Present' : ($edu['end_year'] ?? 'N/A') ?>
                                </td>
                                <td>
                                    <span class="badge <?= $edu['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $edu['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= $edu['sort_order'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/Tamim/admin/education/edit.php?id=<?= $edu['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $edu['id'] ?>, '/Tamim/admin/education/delete.php', 'Are you sure you want to delete this education?')"
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