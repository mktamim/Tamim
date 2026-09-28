<?php
/**
 * Admin Testimonials List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Testimonials';
$currentPage = 'testimonials';

$testimonials = db_all('SELECT * FROM testimonials ORDER BY sort_order, created_at DESC');

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/testimonials/create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Testimonial
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($testimonials)): ?>
            <div class="text-center py-5">
                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No testimonials yet</h5>
                <p class="text-muted">Add your first client testimonial</p>
                <a href="/admin/testimonials/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Add Testimonial
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Client</th>
                            <th>Review</th>
                            <th style="width: 80px;">Rating</th>
                            <th style="width: 100px;">Featured</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $index => $testimonial): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($testimonial['client_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/testimonials/' . $testimonial['client_image']) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover;" class="rounded-circle me-2">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <strong><?= e($testimonial['client_name']) ?></strong>
                                    <?php if ($testimonial['client_designation']): ?>
                                        <br><small class="text-muted"><?= e($testimonial['client_designation']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 300px;"><?= e(mb_strimwidth($testimonial['review'], 0, 80, '...')) ?></span>
                                </td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                    <?php endfor; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $testimonial['is_featured'] ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                                        <?= $testimonial['is_featured'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $testimonial['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= $testimonial['sort_order'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/testimonials/edit.php?id=<?= $testimonial['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $testimonial['id'] ?>, '/admin/testimonials/delete.php', 'Are you sure you want to delete this testimonial?')"
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
