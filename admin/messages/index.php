<?php
/**
 * Admin Messages List
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Messages';
$currentPage = 'messages';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = config('pagination.per_page');
$offset = ($page - 1) * $perPage;

$total = db_one('SELECT COUNT(*) as c FROM messages WHERE is_archived = 0')['c'] ?? 0;
$messages = db_all('SELECT * FROM messages WHERE is_archived = 0 ORDER BY created_at DESC LIMIT ? OFFSET ?', [$perPage, $offset]);

$pagination = paginate($total, $perPage, $page, '/admin/messages/');

// Mark as read if requested
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    db_execute('UPDATE messages SET is_read = 1 WHERE id = ?', [(int)$_GET['read']]);
    redirect('/admin/messages/');
}

// Archive if requested
if (isset($_GET['archive']) && is_numeric($_GET['archive'])) {
    db_execute('UPDATE messages SET is_archived = 1 WHERE id = ?', [(int)$_GET['archive']]);
    redirect('/admin/messages/');
}

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <span class="badge bg-primary"><?= $total ?> total</span>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No messages</h5>
                <p class="text-muted">Contact form messages will appear here</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Name / Email</th>
                            <th style="width: 200px;">Subject</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="<?= !$msg['is_read'] ? 'fw-bold' : '' ?>">
                                <td><?= $msg['id'] ?></td>
                                <td>
                                    <strong><?= e($msg['name']) ?></strong><br>
                                    <small class="text-muted"><?= e($msg['email']) ?></small>
                                    <?php if ($msg['phone']): ?>
                                        <br><small class="text-muted"><i class="fas fa-phone me-1"></i><?= e($msg['phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?= !$msg['is_read'] ? 'fw-bold' : '' ?>"><?= e($msg['subject'] ?: 'No subject') ?></span>
                                    <br><small class="text-muted text-truncate d-inline-block" style="max-width: 180px;"><?= e(mb_strimwidth($msg['message'], 0, 60, '...')) ?></small>
                                </td>
                                <td><?= time_ago($msg['created_at']) ?></td>
                                <td>
                                    <?php if (!$msg['is_read']): ?>
                                        <span class="badge bg-primary">Unread</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/messages/view.php?id=<?= $msg['id'] ?>" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!$msg['is_read']): ?>
                                            <a href="/admin/messages/?read=<?= $msg['id'] ?>" class="btn btn-outline-success" title="Mark as Read">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/admin/messages/?archive=<?= $msg['id'] ?>" class="btn btn-outline-secondary" title="Archive" onclick="return confirm('Archive this message?')">
                                            <i class="fas fa-archive"></i>
                                        </a>
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
