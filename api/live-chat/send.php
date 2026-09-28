<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$sessionId = $_COOKIE['live_chat_session'] ?? null;
$message = trim($_POST['message'] ?? '');
$senderType = $_POST['sender_type'] ?? 'visitor'; // 'visitor' or 'admin'

if (!$sessionId || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing session or message']);
    exit;
}

$chat = db_one('SELECT * FROM live_chats WHERE session_id = ? AND status != "closed" ORDER BY created_at DESC LIMIT 1', [$sessionId]);

if (!$chat) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Chat not found']);
    exit;
}

$senderId = null;
if ($senderType === 'admin') {
    if (!is_logged_in()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $senderId = $_SESSION['admin_id'];
    db_execute('UPDATE live_chats SET status = "active", assigned_admin_id = ?, last_activity_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [$senderId, $chat['id']]);
} else {
    db_execute('UPDATE live_chats SET status = "active", last_activity_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [$chat['id']]);
}

$messageId = db_execute(
    'INSERT INTO live_chat_messages (chat_id, sender_type, sender_id, message) VALUES (?, ?, ?, ?)',
    [$chat['id'], $senderType, $senderId, $message]
);

echo json_encode([
    'success' => true,
    'message_id' => $messageId,
    'timestamp' => date('c')
]);