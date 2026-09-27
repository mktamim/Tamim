<?php
/**
 * Admin Blog List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

$pageTitle = 'Blog';
$currentPage = 'blog';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = config('pagination.per_page');
$offset = ($page - 1) * $perPage;

$status = $_GET['status'] ?? 'all';
$where = '';
$params = [];

if ($status !== 'all') {
    $where = 'WHERE bp.status = ?';
    $params[] = $status;
}

$total = db_one('SELECT COUNT(*) as c FROM blog_posts bp ' . $where, $params)['c'] ?? 0;
$posts = db_all('SELECT bp.*, bc.name as category_name, a.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN admins a ON bp.author_id = a.id ' . $where . ' ORDER BY bp.created_at DESC LIMIT ? OFFSET ?', array_merge($params, [$perPage, $offset]));

$pagination = paginate($total, $perPage, $page, '/Tamim/admin/blog/?status=' . $status);

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/Tamim/admin/blog/create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Post
    </a>
</div>

<!-- Status Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="/Tamim/admin/blog/?status=all" class="btn btn-outline-<?= $status === 'all' ? 'primary' : 'secondary' ?>">All</a>
            <a href="/Tamim/admin/blog/?status=published" class="btn btn-outline-<?= $status === 'published' ? 'success' : 'secondary' ?>">Published</a>
            <a href="/Tamim/admin/blog/?status=draft" class="btn btn-outline-<?= $status === 'draft' ? 'warning' : 'secondary' ?>">Drafts</a>
            <a href="/Tamim/admin/blog/?status=archived" class="btn btn-outline-<?= $status === 'archived' ? 'secondary' : 'secondary' ?>">Archived</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($posts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No blog posts yet</h5>
                <a href="/Tamim/admin/blog/create.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Write First Post
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 80px;">Image</th>
                            <th>Title</th>
                            <th style="width: 120px;">Category</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width": 120px;">Author</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $index => $post): ?>
                            <tr>
                                <td><?= $offset + $index + 1 ?></td>
                                <td>
                                    <?php if ($post['featured_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/blog/' . $post['featured_image']) ?>" alt="" style="width: 60px; height: 40px; object-fit: cover;" class="rounded">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($post['title']) ?></strong>
                                    <?php if ($post['slug']): ?>
                                        <br><small class="text-muted">/blog/<?= e($post['slug']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($post['category_name']): ?>
                                        <span class="badge bg-info"><?= e($post['category_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'published' => 'bg-success',
                                        'draft' => 'bg-warning text-dark',
                                        'archived' => 'bg-secondary'
                                    ];
                                    $cls = $statusClass[$post['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $cls ?>">
                                        <?= ucfirst($post['status']) ?>
                                        <?php if ($post['is_featured']): ?>
                                            <i class="fas fa-star ms-1"></i>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><?= e($post['author_name'] ?? 'Admin') ?></td>
                                <td>
                                    <?php if ($post['published_at']): ?>
                                        <?= format_date($post['published_at']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not published</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/Tamim/admin/blog/edit.php?id=<?= $post['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($post['status'] === 'published'): ?>
                                            <a href="/Tamim/blog/<?= e($post['slug']) ?>" target="_blank" class="btn btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteItem(<?= $post['id'] ?>, '/Tamim/admin/blog/delete.php', 'Are you sure you want to delete this post?')"
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
            
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="card-footer">
                    <?= render_pagination($pagination) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>