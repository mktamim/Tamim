<?php
/**
 * Admin Skill Delete
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);
$csrfToken = $input['csrf_token'] ?? '';

if (!$id || !csrf_verify($csrfToken)) {
    json_response(['success' => false, 'message' => 'Invalid request'], 400);
}

$skill = db_one('SELECT icon_image FROM skills WHERE id = ?', [$id]);
if (!$skill) {
    json_response(['success' => false, 'message' => 'Skill not found'], 404);
}

if ($skill['icon_image']) {
    delete_file('skills/' . $skill['icon_image']);
}

db_execute('DELETE FROM skills WHERE id = ?', [$id]);

json_response(['success' => true, 'message' => 'Skill deleted successfully']);