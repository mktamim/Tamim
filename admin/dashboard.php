<?php
/**
 * Admin Dashboard
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../includes/bootstrap.php';

// Require authentication
if (!auth_check()) {
    redirect('/admin/login.php');
}

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Get statistics
$stats = [
    'projects' => db_one('SELECT COUNT(*) as c FROM projects WHERE is_active = 1')['c'] ?? 0,
    'services' => db_one('SELECT COUNT(*) as c FROM services WHERE is_active = 1')['c'] ?? 0,
    'skills' => db_one('SELECT COUNT(*) as c FROM skills WHERE is_active = 1')['c'] ?? 0,
    'testimonials' => db_one('SELECT COUNT(*) as c FROM testimonials WHERE is_active = 1')['c'] ?? 0,
    'messages' => db_one('SELECT COUNT(*) as c FROM messages WHERE is_archived = 0')['c'] ?? 0,
    'unread_messages' => db_one('SELECT COUNT(*) as c FROM messages WHERE is_read = 0 AND is_archived = 0')['c'] ?? 0,
    'blog_posts' => db_one('SELECT COUNT(*) as c FROM blog_posts WHERE status = "published"')['c'] ?? 0,
    'experiences' => db_one('SELECT COUNT(*) as c FROM experiences WHERE is_active = 1')['c'] ?? 0,
];

// Recent messages
$recentMessages = db_all('SELECT * FROM messages WHERE is_archived = 0 ORDER BY created_at DESC LIMIT 5');

// Recent projects
$recentProjects = db_all('SELECT * FROM projects WHERE is_active = 1 ORDER BY created_at DESC LIMIT 5');

require __DIR__ . '/../includes/admin_header.php';
?>
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light">
                <i class="fas fa-folder-open text-primary"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['projects'] ?></h3>
                <p class="stat-label">Projects</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-success-light">
                <i class="fas fa-briefcase text-success"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['services'] ?></h3>
                <p class="stat-label">Services</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="fas fa-code-branch text-warning"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['skills'] ?></h3>
                <p class="stat-label">Skills</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-info-light">
                <i class="fas fa-star text-info"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['testimonials'] ?></h3>
                <p class="stat-label">Testimonials</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light">
                <i class="fas fa-envelope text-danger"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['messages'] ?></h3>
                <p class="stat-label">Messages</p>
                <?php if ($stats['unread_messages'] > 0): ?>
                    <span class="badge bg-danger"><?= $stats['unread_messages'] ?> unread</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-purple-light">
                <i class="fas fa-blog text-purple"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['blog_posts'] ?></h3>
                <p class="stat-label">Blog Posts</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-teal-light">
                <i class="fas fa-briefcase text-teal"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $stats['experiences'] ?></h3>
                <p class="stat-label">Experience</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
    <!-- Recent Messages -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Recent Messages</h5>
                <a href="/admin/messages/" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMessages)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">No messages yet</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMessages as $msg): ?>
                            <a href="/admin/messages/view.php?id=<?= $msg['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1 <?= !$msg['is_read'] ? 'fw-bold' : '' ?>">
                                        <?= e($msg['name']) ?>
                                        <?php if (!$msg['is_read']): ?>
                                            <span class="badge bg-primary ms-2">New</span>
                                        <?php endif; ?>
                                    </h6>
                                    <small class="text-muted"><?= time_ago($msg['created_at']) ?></small>
                                </div>
                                <p class="mb-1 text-truncate"><?= e($msg['subject'] ?: 'No subject') ?></p>
                                <small class="text-muted"><?= e($msg['email']) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i>Recent Projects</h5>
                <a href="/admin/projects/" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentProjects)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                        <p class="mb-0">No projects yet</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentProjects as $project): ?>
                            <a href="/admin/projects/edit.php?id=<?= $project['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <?php if ($project['cover_image']): ?>
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $project['cover_image']) ?>" alt="" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= e($project['title']) ?></h6>
                                        <small class="text-muted"><?= e($project['category_name'] ?? 'Uncategorized') ?></small>
                                    </div>
                                    <span class="badge <?= $project['is_featured'] ? 'bg-warning' : 'bg-secondary' ?>">
                                        <?= $project['is_featured'] ? 'Featured' : 'Normal' ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="/admin/projects/create.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Add Project
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/services/create.php" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Add Service
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/skills/create.php" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Add Skill
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/testimonials/create.php" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Add Testimonial
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/experience/create.php" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Add Experience
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/education/create.php" class="btn btn-outline-dark w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Add Education
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/blog/create.php" class="btn btn-outline-purple w-100 py-3">
                            <i class="fas fa-plus fa-2x d-block mb-2"></i>
                            Write Blog Post
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/settings/" class="btn btn-outline-teal w-100 py-3">
                            <i class="fas fa-cog fa-2x d-block mb-2"></i>
                            Site Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
