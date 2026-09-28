<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/live-chat/');
}

$id = (int)($_POST['id'] ?? 0);

if (!$id || !csrf_verify($_POST['csrf_token'] ?? '')) {
    redirect('/admin/live-chat/', 'Invalid request', 'danger');
}

$chat = db_one('SELECT * FROM live_chats WHERE id = ?', [$id]);
if (!$chat) {
    redirect('/admin/live-chat/', 'Chat not found', 'danger');
}

if ($chat['status'] === 'closed') {
    redirect('/admin/live-chat/', 'Chat already closed', 'warning');
}

if ($chat['assigned_admin_id'] && $chat['assigned_admin_id'] != $_SESSION['admin_id']) {
    redirect('/admin/live-chat/view.php?id=' . $id, 'You are not assigned to this chat', 'danger');
}

db_execute('UPDATE live_chats SET status = "closed", closed_at = CURRENT_TIMESTAMP WHERE id = ?', [$id]);

redirect('/admin/live-chat/', 'Chat closed successfully', 'success');
