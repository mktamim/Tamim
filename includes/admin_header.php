<?php
/**
 * Admin Header
 * Portfolio Website - Admin Includes
 */
$pageTitle = $pageTitle ?? 'Admin Panel';
$currentPage = $currentPage ?? '';
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
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
            --header-height: 64px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="/admin/dashboard.php" class="sidebar-brand">
                    <i class="fas fa-code brand-icon"></i>
                    <span class="brand-text">Admin Panel</span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($currentPage, ['settings', 'homepage', 'about']) ? 'active' : '' ?>" href="/admin/settings/">
                            <i class="fas fa-cog nav-icon"></i>
                            <span class="nav-text">Website Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'homepage' ? 'active' : '' ?>" href="/admin/homepage/">
                            <i class="fas fa-home nav-icon"></i>
                            <span class="nav-text">Homepage</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="/admin/about/">
                            <i class="fas fa-user nav-icon"></i>
                            <span class="nav-text">About</span>
                        </a>
                    </li>
                    <li class="nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'skills' ? 'active' : '' ?>" href="/admin/skills/">
                            <i class="fas fa-code-branch nav-icon"></i>
                            <span class="nav-text">Skills</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'services' ? 'active' : '' ?>" href="/admin/services/">
                            <i class="fas fa-briefcase nav-icon"></i>
                            <span class="nav-text">Services</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'projects' ? 'active' : '' ?>" href="/admin/projects/">
                            <i class="fas fa-folder-open nav-icon"></i>
                            <span class="nav-text">Projects</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'experience' ? 'active' : '' ?>" href="/admin/experience/">
                            <i class="fas fa-briefcase nav-icon"></i>
                            <span class="nav-text">Experience</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'education' ? 'active' : '' ?>" href="/admin/education/">
                            <i class="fas fa-graduation-cap nav-icon"></i>
                            <span class="nav-text">Education</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'testimonials' ? 'active' : '' ?>" href="/admin/testimonials/">
                            <i class="fas fa-star nav-icon"></i>
                            <span class="nav-text">Testimonials</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'messages' ? 'active' : '' ?>" href="/admin/messages/">
                            <i class="fas fa-envelope nav-icon"></i>
                            <span class="nav-text">Messages</span>
                            <?php 
                            $unreadCount = db_one('SELECT COUNT(*) as c FROM messages WHERE is_read = 0')['c'] ?? 0;
                            if ($unreadCount > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-auto"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'live-chat' ? 'active' : '' ?>" href="/admin/live-chat/">
                            <i class="fas fa-comments nav-icon"></i>
                            <span class="nav-text">Live Chat</span>
                            <?php 
                            $liveChatWaiting = db_one('SELECT COUNT(*) as c FROM live_chats WHERE status = "waiting"')['c'] ?? 0;
                            if ($liveChatWaiting > 0): ?>
                                <span class="badge bg-warning rounded-pill ms-auto"><?= $liveChatWaiting ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'blog' ? 'active' : '' ?>" href="/admin/blog/">
                            <i class="fas fa-blog nav-icon"></i>
                            <span class="nav-text">Blog</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="/" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-external-link-alt me-2"></i><span class="d-none d-sm-inline">View Website</span>
                </a>
                <a href="/admin/logout.php" class="btn btn-outline-danger w-100">
                    <i class="fas fa-sign-out-alt me-2"></i><span class="d-none d-sm-inline">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="mobile-sidebar-toggle" id="mobileSidebarToggle" aria-label="Open sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title"><?= e($pageTitle) ?></h1>
                </div>
                <div class="header-right">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle header-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?php $user = auth_user(); ?>
                                <?php if ($user && $user['avatar']): ?>
                                    <img src="<?= e($user['avatar']) ?>" alt="" class="rounded-circle">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <span class="d-none d-md-inline ms-2"><?= e($user['full_name'] ?? $user['username'] ?? 'Admin') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Account</h6></li>
                            <li><a class="dropdown-item" href="/admin/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="/admin/change-password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="admin-content">
                <div class="container-fluid">
                    <?php if ($flash = flash()): ?>
                        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                            <?= e($flash['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>