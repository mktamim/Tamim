<?php
/**
 * Admin Message Reply
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/messages/');
}

if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
    redirect('/admin/messages/', 'Invalid CSRF token.', 'danger');
}

$id = (int)($_POST['message_id'] ?? 0);
$subject = trim($_POST['subject'] ?? '');
$replyMessage = trim($_POST['reply_message'] ?? '');

if (!$id || empty($replyMessage)) {
    redirect('/admin/messages/', 'Invalid request.', 'danger');
}

$message = db_one('SELECT * FROM messages WHERE id = ?', [$id]);
if (!$message) {
    redirect('/admin/messages/', 'Message not found.', 'danger');
}

// Update message with reply
db_execute('UPDATE messages SET is_read = 1, replied_at = NOW(), reply_message = ? WHERE id = ?', [$replyMessage, $id]);

// TODO: Send actual email here if needed
// mail($message['email'], $subject, $replyMessage, $headers);

redirect('/admin/messages/view.php?id=' . $id, 'Reply saved successfully!');
