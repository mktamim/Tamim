<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$sessionId = $_COOKIE['live_chat_session'] ?? null;
$since = isset($_GET['since']) ? (int)$_GET['since'] : 0;

if (!$sessionId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No session']);
    exit;
}

$chat = db_one('SELECT * FROM live_chats WHERE session_id = ? AND status != "closed" ORDER BY created_at DESC LIMIT 1', [$sessionId]);

if (!$chat) {
    echo json_encode(['success' => true, 'messages' => [], 'status' => 'no_chat']);
    exit;
}

$messages = db_all(
    'SELECT m.*, a.full_name as admin_name FROM live_chat_messages m LEFT JOIN admins a ON m.sender_id = a.id WHERE m.chat_id = ? AND m.id > ? ORDER BY m.created_at ASC',
    [$chat['id'], $since]
);

$unreadCount = 0;
if (is_logged_in() && $_SESSION['admin_id'] == ($chat['assigned_admin_id'] ?? 0)) {
    $unreadCount = db_one('SELECT COUNT(*) as cnt FROM live_chat_messages WHERE chat_id = ? AND sender_type = "visitor" AND is_read = FALSE', [$chat['id']])['cnt'] ?? 0;
} elseif (!is_logged_in()) {
    $unreadCount = db_one('SELECT COUNT(*) as cnt FROM live_chat_messages WHERE chat_id = ? AND sender_type = "admin" AND is_read = FALSE', [$chat['id']])['cnt'] ?? 0;
}

echo json_encode([
    'success' => true,
    'chat_id' => $chat['id'],
    'status' => $chat['status'],
    'messages' => $messages,
    'unread_count' => $unreadCount
]);