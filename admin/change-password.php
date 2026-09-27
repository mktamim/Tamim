<?php
/**
 * Admin Change Password
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/Tamim/admin/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/Tamim/admin/profile.php');
}

if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
    redirect('/Tamim/admin/profile.php', 'Invalid CSRF token.', 'danger');
}

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$admin = auth_user();
$errors = [];

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $errors[] = 'All fields are required.';
}

if (!password_verify($currentPassword, $admin['password'])) {
    $errors[] = 'Current password is incorrect.';
}

if (strlen($newPassword) < config('security.password_min_length')) {
    $errors[] = 'New password must be at least ' . config('security.password_min_length') . ' characters.';
}

if ($newPassword !== $confirmPassword) {
    $errors[] = 'New passwords do not match.';
}

if (!empty($errors)) {
    $_SESSION['password_errors'] = $errors;
    redirect('/Tamim/admin/profile.php');
}

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
db_execute('UPDATE admins SET password = ?, updated_at = NOW() WHERE id = ?', [$hashedPassword, $admin['id']]);

redirect('/Tamim/admin/profile.php', 'Password changed successfully!');