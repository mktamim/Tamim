<?php
/**
 * Admin Blog Delete
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

$post = db_one('SELECT featured_image FROM blog_posts WHERE id = ?', [$id]);
if (!$post) {
    json_response(['success' => false, 'message' => 'Post not found'], 404);
}

if ($post['featured_image']) {
    delete_file('blog/' . $post['featured_image']);
}

db_execute('DELETE FROM blog_posts WHERE id = ?', [$id]);

json_response(['success' => true, 'message' => 'Post deleted successfully']);
