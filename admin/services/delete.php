<?php
/**
 * Admin Service Delete
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

$service = db_one('SELECT icon_image FROM services WHERE id = ?', [$id]);
if (!$service) {
    json_response(['success' => false, 'message' => 'Service not found'], 404);
}

if ($service['icon_image']) {
    delete_file('services/' . $service['icon_image']);
}

db_execute('DELETE FROM services WHERE id = ?', [$id]);

json_response(['success' => true, 'message' => 'Service deleted successfully']);
