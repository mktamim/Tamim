<?php
/**
 * Admin Login
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../includes/bootstrap.php';

// Redirect if already logged in
if (auth_check()) {
    redirect('/admin/dashboard.php');
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($email) || empty($password)) {
            $errors[] = 'Email and password are required.';
        } elseif (!valid_email($email)) {
            $errors[] = 'Invalid email format.';
        } else {
            $admin = db_one('SELECT * FROM admins WHERE email = ?', [$email]);
            if (!$admin || !password_verify($password, $admin['password'])) {
                // Track failed attempts
                $attempts = ($admin['login_attempts'] ?? 0) + 1;
                $lockout = null;
                if ($attempts >= config('security.max_login_attempts')) {
                    $lockout = date('Y-m-d H:i:s', time() + config('security.lockout_time'));
                }
                db_execute('UPDATE admins SET login_attempts = ?, locked_until = ? WHERE email = ?', [$attempts, $lockout, $email]);
                $errors[] = 'Invalid email or password.';
            } elseif ($admin['locked_until'] && new DateTime($admin['locked_until']) > new DateTime()) {
                $errors[] = 'Account temporarily locked. Please try again later.';
            } else {
                auth_login($admin['id'], $remember);
                redirect('/admin/dashboard.php', 'Welcome back, ' . e($admin['full_name'] ?? $admin['username']) . '!');
            }
        }
    }
    $old = ['email' => $email ?? ''];
}

$pageTitle = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle) ?> | Admin Panel</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: <?= e(setting('primary_color', '#2563eb')) ?>;
            --secondary-color: <?= e(setting('secondary_color', '#0ea5e9')) ?>;
        }
    </style>
</head>
<body>
<div class="admin-login-page">
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <div class="admin-login-logo">
                    <i class="fas fa-code"></i>
                </div>
                <h1>Admin Login</h1>
                <p class="text-muted">Enter your credentials to access the dashboard</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="admin-login-form" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="email" placeholder="admin@example.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="admin-login-footer mt-4 text-center">
                <p class="mb-0 text-muted small">
                    <strong>Demo Credentials:</strong><br>
                    Email: admin@example.com<br>
                    Password: admin123
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.admin-login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 20px;
}
.admin-login-container {
    width: 100%;
    max-width: 420px;
}
.admin-login-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    padding: 40px;
    border: 1px solid rgba(0, 0, 0, 0.05);
}
.admin-login-header {
    text-align: center;
    margin-bottom: 32px;
}
.admin-login-logo {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--primary-color, #2563eb), var(--secondary-color, #0ea5e9));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 28px;
    color: white;
}
.admin-login-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}
.admin-login-header p {
    font-size: 14px;
    color: #6b7280;
}
.admin-login-form .input-group-text {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-right: none;
    color: #6b7280;
}
.admin-login-form .form-control {
    border: 1px solid #e5e7eb;
    border-left: none;
    padding: 12px 16px;
    font-size: 14px;
}
.admin-login-form .form-control:focus {
    border-color: var(--primary-color, #2563eb);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.admin-login-form .form-control:focus + .input-group-text {
    border-color: var(--primary-color, #2563eb);
    background: white;
}
.admin-login-form .btn-primary {
    padding: 14px;
    font-weight: 600;
    font-size: 15px;
    border-radius: 10px;
}
.admin-login-footer {
    padding-top: 20px;
    border-top: 1px solid #f3f4f6;
}
</style>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
