<?php
/**
 * Admin Testimonial Delete
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

$testimonial = db_one('SELECT client_image FROM testimonials WHERE id = ?', [$id]);
if (!$testimonial) {
    json_response(['success' => false, 'message' => 'Testimonial not found'], 404);
}

if ($testimonial['client_image']) {
    delete_file('testimonials/' . $testimonial['client_image']);
}

db_execute('DELETE FROM testimonials WHERE id = ?', [$id]);

json_response(['success' => true, 'message' => 'Testimonial deleted successfully']);