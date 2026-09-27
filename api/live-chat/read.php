<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$sessionId = $_COOKIE['live_chat_session'] ?? null;
$messageIds = $_POST['message_ids'] ?? [];
if (is_string($messageIds)) {
    $messageIds = json_decode($messageIds, true) ?? [];
}
if (!is_array($messageIds)) {
    $messageIds = [];
}

if (!$sessionId || !$messageIds) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$chat = db_one('SELECT * FROM live_chats WHERE session_id = ? AND status != "closed" ORDER BY created_at DESC LIMIT 1', [$sessionId]);

if (!$chat) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Chat not found']);
    exit;
}

// Verify ownership
if (is_logged_in()) {
    if ($chat['assigned_admin_id'] != $_SESSION['admin_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
    // Admin reads visitor messages
    $placeholders = implode(',', array_fill(0, count($messageIds), '?'));
    db_execute("UPDATE live_chat_messages SET is_read = TRUE WHERE chat_id = ? AND sender_type = 'visitor' AND id IN ($placeholders)", array_merge([$chat['id']], $messageIds));
} else {
    // Visitor reads admin messages
    $placeholders = implode(',', array_fill(0, count($messageIds), '?'));
    db_execute("UPDATE live_chat_messages SET is_read = TRUE WHERE chat_id = ? AND sender_type = 'admin' AND id IN ($placeholders)", array_merge([$chat['id']], $messageIds));
}

echo json_encode(['success' => true]);