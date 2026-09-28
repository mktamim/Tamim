<?php
/**
 * Admin Skills List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Skills';
$currentPage = 'skills';

$skills = db_all('SELECT * FROM skills ORDER BY sort_order, name');

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/skills/create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Skill
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($skills)): ?>
            <div class="text-center py-5">
                <i class="fas fa-code-branch fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No skills yet</h5>
                <p class="text-muted">Add your first skill to get started</p>
                <a href="/admin/skills/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Add Skill
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Icon</th>
                            <th>Name</th>
                            <th style="width: 120px;">Category</th>
                            <th style="width: 100px;">Level</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $index => $skill): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($skill['icon_type'] === 'image' && $skill['icon_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/skills/' . $skill['icon_image']) ?>" alt="" style="width: 32px; height: 32px;" class="rounded">
                                    <?php else: ?>
                                        <i class="<?= e($skill['icon_class'] ?? 'fas fa-code') ?> fa-lg text-primary"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($skill['name']) ?></strong>
                                    <?php if ($skill['description']): ?>
                                        <br><small class="text-muted"><?= e(mb_strimwidth($skill['description'], 0, 50, '...')) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= e($skill['category']) ?></span></td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= (int)$skill['percentage'] ?>%" aria-valuenow="<?= (int)$skill['percentage'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted"><?= (int)$skill['percentage'] ?>%</small>
                                </td>
                                <td>
                                    <span class="badge <?= $skill['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $skill['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= $skill['sort_order'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/skills/edit.php?id=<?= $skill['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $skill['id'] ?>, '/admin/skills/delete.php', 'Are you sure you want to delete this skill?')"
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
