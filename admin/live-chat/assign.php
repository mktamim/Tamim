<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/Tamim/admin/live-chat/');
}

$id = (int)($_POST['id'] ?? 0);
$adminId = (int)($_POST['admin_id'] ?? 0);

if (!$id || !$adminId || !csrf_verify($_POST['csrf_token'] ?? '')) {
    redirect('/Tamim/admin/live-chat/', 'Invalid request', 'danger');
}

$chat = db_one('SELECT * FROM live_chats WHERE id = ?', [$id]);
if (!$chat) {
    redirect('/Tamim/admin/live-chat/', 'Chat not found', 'danger');
}

if ($chat['status'] === 'closed') {
    redirect('/Tamim/admin/live-chat/view.php?id=' . $id, 'Chat is already closed', 'warning');
}

db_execute('UPDATE live_chats SET assigned_admin_id = ?, status = "active", updated_at = CURRENT_TIMESTAMP, last_activity = CURRENT_TIMESTAMP WHERE id = ?', [$adminId, $id]);

redirect('/Tamim/admin/live-chat/view.php?id=' . $id, 'Chat assigned to you', 'success');