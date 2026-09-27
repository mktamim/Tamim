<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$sessionId = $_COOKIE['live_chat_session'] ?? null;

if (!$sessionId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No session']);
    exit;
}

$chat = db_one('SELECT * FROM live_chats WHERE session_id = ? AND status != "closed" ORDER BY created_at DESC LIMIT 1', [$sessionId]);

if (!$chat) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Chat not found']);
    exit;
}

// Verify permission
if (is_logged_in()) {
    if ($chat['assigned_admin_id'] != $_SESSION['admin_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
} else {
    // Visitor can only close their own chat
    // (already verified by session)
}

db_execute('UPDATE live_chats SET status = "closed", closed_at = CURRENT_TIMESTAMP WHERE id = ?', [$chat['id']]);

// Clear session cookie
setcookie('live_chat_session', '', time() - 3600, '/', '', false, true);

echo json_encode(['success' => true, 'message' => 'Chat closed']);