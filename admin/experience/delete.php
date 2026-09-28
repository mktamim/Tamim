<?php
/**
 * Admin Experience Delete
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

$exp = db_one('SELECT company_logo FROM experiences WHERE id = ?', [$id]);
if (!$exp) {
    json_response(['success' => false, 'message' => 'Experience not found'], 404);
}

if ($exp['company_logo']) {
    delete_file('experience/' . $exp['company_logo']);
}

db_execute('DELETE FROM experiences WHERE id = ?', [$id]);

json_response(['success' => true, 'message' => 'Experience deleted successfully']);
