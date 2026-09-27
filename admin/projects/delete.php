<?php
/**
 * Admin Project Delete
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

$project = db_one('SELECT cover_image, gallery_images FROM projects WHERE id = ?', [$id]);
if (!$project) {
    json_response(['success' => false, 'message' => 'Project not found'], 404);
}

if ($project['cover_image']) {
    delete_file('projects/' . $project['cover_image']);
}

if ($project['gallery_images']) {
    $gallery = json_decode($project['gallery_images'], true) ?? [];
    foreach ($gallery as $img) {
        delete_file('projects/gallery/' . $img);
    }
}

db_execute('DELETE FROM projects WHERE id = ?', [$id]);

json_response(['success' => true, 'message' => 'Project deleted successfully']);