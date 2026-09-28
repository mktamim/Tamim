<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

// Prevent caching - critical for API endpoints
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// DEBUG - return cookie info in response
$debugInfo = [
    'cookie_received' => isset($_COOKIE['live_chat_session']),
    'cookie_value' => $_COOKIE['live_chat_session'] ?? 'NOT_SET',
    'all_cookies' => array_keys($_COOKIE)
];

// Get or create session
$sessionId = $_COOKIE['live_chat_session'] ?? null;
$visitorName = trim($_POST['name'] ?? 'Visitor');
$visitorEmail = trim($_POST['email'] ?? '');

if (!$sessionId) {
    $sessionId = bin2hex(random_bytes(32));
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('live_chat_session', $sessionId, [
        'expires' => time() + 86400 * 30,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Check existing chat
$chat = db_one('SELECT * FROM live_chats WHERE session_id = ? AND status != "closed" ORDER BY created_at DESC LIMIT 1', [$sessionId]);

if (!$chat) {
    // Create new chat
    $chatId = db_execute(
        'INSERT INTO live_chats (session_id, visitor_name, visitor_email, visitor_ip, user_agent, status) VALUES (?, ?, ?, ?, ?, "waiting")',
        [$sessionId, $visitorName, $visitorEmail, get_client_ip(), $_SERVER['HTTP_USER_AGENT'] ?? '']
    );
    $chat = db_one('SELECT * FROM live_chats WHERE id = ?', [$chatId]);
} else {
    // Update visitor info
    db_execute('UPDATE live_chats SET visitor_name = ?, visitor_email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [$visitorName, $visitorEmail, $chat['id']]);
    $chatId = $chat['id'];
}

echo json_encode([
    'success' => true,
    'chat_id' => $chatId,
    'session_id' => $sessionId,
    'status' => $chat['status'] ?? 'waiting',
    'debug' => $debugInfo
]);