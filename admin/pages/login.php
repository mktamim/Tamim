<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | <?php echo tamim_e(tamim_setting('site_name', 'Tamim')); ?> Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-login-body">
<main class="admin-login-shell">
    <section class="admin-login-card" aria-labelledby="login-title">
        <div class="admin-login-brand"><span>T</span><strong>Tamim</strong><small>Admin</small></div>
        <p class="admin-eyebrow">Portfolio control</p>
        <h1 id="login-title">Welcome back</h1>
        <p class="admin-login-intro">Sign in to manage your website content.</p>

        <?php if ($loginMessage !== ''): ?>
            <div class="admin-notice admin-notice-<?php echo tamim_e($loginMessageType); ?>" role="alert"><?php echo tamim_e($loginMessage); ?></div>
        <?php endif; ?>

        <form class="admin-form admin-login-form" method="post" action="/admin/login">
            <?php echo tamim_csrf_field(); ?>
            <label>
                <span>Email address</span>
                <input type="email" name="email" value="<?php echo tamim_e($loginEmail); ?>" required autocomplete="username" autofocus>
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="admin-button admin-button-primary" type="submit">Sign in <span>→</span></button>
        </form>
        <p class="admin-login-foot">Use the administrator account created during installation.</p>
    </section>
    <aside class="admin-login-aside" aria-hidden="true">
        <div class="admin-login-aside-inner">
            <p class="admin-eyebrow">A clearer way to publish</p>
            <h2>Keep every detail <em>intentional.</em></h2>
            <p>Update your story, showcase your work and stay close to the people who reach out.</p>
            <div class="admin-login-rule"></div>
            <span class="admin-login-mark">T</span>
        </div>
    </aside>
</main>
</body>
</html>
