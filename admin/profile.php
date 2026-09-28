<?php
/**
 * Admin Profile
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Profile';
$currentPage = 'profile';
$admin = auth_user();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $avatar = $admin['avatar'];
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required.';
        }
        
        if (empty($email) || !valid_email($email)) {
            $errors[] = 'Valid email is required.';
        } else {
            $existing = db_one('SELECT id FROM admins WHERE email = ? AND id != ?', [$email, $admin['id']]);
            if ($existing) {
                $errors[] = 'Email already in use.';
            }
        }
        
        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $result = upload_image($_FILES['avatar'], 'avatars');
            if ($result['success']) {
                if ($admin['avatar']) {
                    delete_file('avatars/' . $admin['avatar']);
                }
                $avatar = $result['filename'];
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        }
        
        if (empty($errors)) {
            db_execute('UPDATE admins SET full_name = ?, email = ?, avatar = ?, updated_at = NOW() WHERE id = ?', [$fullName, $email, $avatar, $admin['id']]);
            $_SESSION['admin_name'] = $fullName;
            $success = 'Profile updated successfully!';
            $admin = auth_user(); // Refresh
        }
    }
}

require __DIR__ . '/../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
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

<?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="text-center mb-4">
                        <?php if ($admin['avatar']): ?>
                            <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/avatars/' . $admin['avatar']) ?>" alt="" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 48px; font-weight: 700;">
                                <?= strtoupper(substr($admin['full_name'] ?? $admin['username'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="avatar" accept="image/*" data-preview="avatar_preview">
                        <div class="form-text">Recommended: 200x200px. Max 5MB.</div>
                        <img id="avatar_preview" src="" alt="" class="rounded-circle mt-2 d-none" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= e($admin['username']) ?>" readonly>
                        <div class="form-text">Username cannot be changed.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" value="<?= e($admin['full_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= e($admin['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Last Login</label>
                        <input type="text" class="form-control" value="<?= $admin['last_login'] ? format_date($admin['last_login'], 'M d, Y H:i') : 'Never' ?>" readonly>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/change-password.php">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" required minlength="8">
                        <div class="form-text">Minimum 8 characters.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
