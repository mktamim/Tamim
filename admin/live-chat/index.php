<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_admin();

$pageTitle = 'Live Chat';
$currentPage = 'live-chat';

$status = $_GET['status'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];

if ($status !== 'all') {
    if ($status === 'expired') {
        $where = 'WHERE lc.status IN ("waiting","active") AND TIMESTAMPDIFF(MINUTE, lc.last_activity, NOW()) >= 5';
    } else {
        $where = 'WHERE lc.status = ?';
        $params[] = $status;
    }
}

$total = db_one('SELECT COUNT(*) as cnt FROM live_chats lc ' . $where, $params)['cnt'];
$chats = db_all(
    'SELECT lc.*, a.full_name as admin_name,
        (SELECT COUNT(*) FROM live_chat_messages WHERE chat_id = lc.id AND sender_type = "visitor" AND is_read = FALSE) as unread_visitor,
        (SELECT COUNT(*) FROM live_chat_messages WHERE chat_id = lc.id AND sender_type = "admin" AND is_read = FALSE) as unread_admin,
        (SELECT message FROM live_chat_messages WHERE chat_id = lc.id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM live_chat_messages WHERE chat_id = lc.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
        TIMESTAMPDIFF(MINUTE, lc.last_activity, NOW()) as inactive_minutes
    FROM live_chats lc
    LEFT JOIN admins a ON lc.assigned_admin_id = a.id
    ' . $where . '
    ORDER BY lc.updated_at DESC
    LIMIT ? OFFSET ?',
    array_merge($params, [$perPage, $offset])
);

$totalPages = ceil($total / $perPage);

$stats = [
    'waiting' => db_one('SELECT COUNT(*) as cnt FROM live_chats WHERE status = "waiting"')['cnt'],
    'active' => db_one('SELECT COUNT(*) as cnt FROM live_chats WHERE status = "active"')['cnt'],
    'closed' => db_one('SELECT COUNT(*) as cnt FROM live_chats WHERE status = "closed"')['cnt'],
    'expired' => db_one('SELECT COUNT(*) as cnt FROM live_chats WHERE status IN ("waiting","active") AND TIMESTAMPDIFF(MINUTE, last_activity, NOW()) >= 5')['cnt'],
];

require __DIR__ . '/../../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Live Chat</h1>
    <div class="d-flex gap-2">
        <a href="/Tamim/admin/live-chat/" class="btn btn-outline-primary">All</a>
        <a href="/Tamim/admin/live-chat/?status=waiting" class="btn btn-outline-warning">Waiting (<?= $stats['waiting'] ?>)</a>
        <a href="/Tamim/admin/live-chat/?status=active" class="btn btn-outline-primary">Active (<?= $stats['active'] ?>)</a>
        <a href="/Tamim/admin/live-chat/?status=expired" class="btn btn-outline-danger">Expired (<?= $stats['expired'] ?>)</a>
        <a href="/Tamim/admin/live-chat/?status=closed" class="btn btn-outline-secondary">Closed (<?= $stats['closed'] ?>)</a>
    </div>
</div>

<?php if (empty($chats)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No chats found</h4>
            <p class="text-muted">Live chat conversations will appear here.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Visitor</th>
                            <th>Status</th>
                            <th>Assigned Admin</th>
                            <th>Last Message</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chats as $chat):
                    $isExpired = $chat['inactive_minutes'] >= 5 && in_array($chat['status'], ['waiting', 'active']);
                ?>
                    <tr class="<?= $chat['unread_visitor'] > 0 ? 'table-warning' : '' ?><?= $isExpired ? ' table-danger' : '' ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white me-2">
                                    <?= strtoupper(substr($chat['visitor_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= e($chat['visitor_name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= e($chat['visitor_email'] ?: 'No email') ?></small>
                                    <?php if ($chat['unread_visitor'] > 0): ?>
                                        <span class="badge bg-warning ms-2"><?= $chat['unread_visitor'] ?> new</span>
                                    <?php endif; ?>
                                    <?php if ($isExpired): ?>
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($isExpired): ?>
                                <span class="badge bg-danger">Expired</span>
                            <?php else: ?>
                                <span class="badge bg-<?= $chat['status'] === 'waiting' ? 'warning' : ($chat['status'] === 'active' ? 'success' : 'secondary') ?>">
                                    <?= ucfirst($chat['status']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                                <td>
                                    <?= $chat['admin_name'] ? e($chat['admin_name']) : '<span class="text-muted">Unassigned</span>' ?>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;">
                                        <?= $chat['last_message'] ? e(mb_strimwidth($chat['last_message'], 0, 80, '...')) : '<span class="text-muted">No messages yet</span>' ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><?= $chat['last_message_time'] ? format_date($chat['last_message_time'], 'M d, H:i') : format_date($chat['updated_at'], 'M d, H:i') ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/Tamim/admin/live-chat/view.php?id=<?= $chat['id'] ?>" class="btn btn-outline-primary" title="View Chat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($chat['status'] !== 'closed'): ?>
                                            <a href="/Tamim/admin/live-chat/assign.php?id=<?= $chat['id'] ?>&admin_id=<?= $_SESSION['admin_id'] ?>" class="btn btn-outline-success" title="Take Chat">
                                                <i class="fas fa-comment-dots"></i>
                                            </a>
                                            <form action="/Tamim/admin/live-chat/close.php" method="POST" style="display:inline;" onsubmit="return confirm('Close this chat?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $chat['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger" title="Close Chat">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Closed</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="card-footer">
                    <nav aria-label="Pagination">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?status=<?= $status ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>