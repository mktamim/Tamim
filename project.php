<?php
/**
 * Project Details Page
 * Portfolio Website
 */

require_once __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h1>404 - Project Not Found</h1><a href="/" class="btn btn-primary mt-3">Back to Home</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$project = project_by_slug($slug);
if (!$project) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h1>404 - Project Not Found</h1><a href="/" class="btn btn-primary mt-3">Back to Home</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Increment views
db_execute('UPDATE projects SET views = views + 1 WHERE id = ?', [$project['id']]);

$pageTitle = $project['title'] . ' | ' . setting('site_name');
$pageDescription = $project['short_description'] ?? $project['full_description'] ?? setting('seo_description');
$pageUrl = current_url();
$pageImage = $project['cover_image'] ? setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $project['cover_image'] : '';
$pageType = 'article';

require __DIR__ . '/includes/header.php';
?>

<!-- Project Header -->
<section class="section" style="padding-top: 140px; padding-bottom: 60px;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/#projects">Projects</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($project['title']) ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <span class="badge bg-primary mb-3"><?= e($project['category_name'] ?? 'General') ?></span>
                <h1 class="display-4 fw-bold mb-4"><?= e($project['title']) ?></h1>
                
                <?php if ($project['project_date']): ?>
                    <div class="text-muted mb-4">
                        <i class="fas fa-calendar me-2"></i><?= format_date($project['project_date'], 'F Y') ?>
                        <?php if ($project['client_name']): ?>
                            | <i class="fas fa-user me-2 ms-3"></i><?= e($project['client_name']) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($project['live_url'] || $project['github_url']): ?>
                    <div class="mb-4">
                        <?php if ($project['live_url']): ?>
                            <a href="<?= e($project['live_url']) ?>" target="_blank" class="btn btn-primary me-2">
                                <i class="fas fa-external-link-alt me-2"></i>Live Demo
                            </a>
                        <?php endif; ?>
                        <?php if ($project['github_url']): ?>
                            <a href="<?= e($project['github_url']) ?>" target="_blank" class="btn btn-outline-dark">
                                <i class="fab fa-github me-2"></i>View Code
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($project['cover_image']): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $project['cover_image']) ?>" alt="<?= e($project['title']) ?>" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Project Details -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if ($project['full_description']): ?>
                    <div class="mb-5">
                        <h2 class="h3 mb-4">Project Overview</h2>
                        <div class="project-description" style="line-height: 1.8; font-size: 16px; color: var(--text-color);">
                            <?= $project['full_description'] ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php 
                $features = json_decode($project['features'] ?? '[]', true) ?? [];
                if (!empty($features)): ?>
                    <div class="mb-5">
                        <h2 class="h3 mb-4">Key Features</h2>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($features as $feature): ?>
                                <li class="list-group-item px-0 border-0">
                                    <i class="fas fa-check-circle text-success me-2"></i><?= e($feature) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if ($project['challenges']): ?>
                    <div class="mb-5">
                        <h2 class="h3 mb-4">Challenges</h2>
                        <div class="p-4 bg-light rounded-3">
                            <?= $project['challenges'] ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($project['solution']): ?>
                    <div class="mb-5">
                        <h2 class="h3 mb-4">Solution</h2>
                        <div class="p-4 bg-light rounded-3">
                            <?= $project['solution'] ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php 
                $gallery = json_decode($project['gallery_images'] ?? '[]', true) ?? [];
                if (!empty($gallery)): ?>
                    <div class="mb-5">
                        <h2 class="h3 mb-4">Project Gallery</h2>
                        <div class="row g-3">
                            <?php foreach ($gallery as $img): ?>
                                <div class="col-md-4">
                                    <a href="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/gallery/' . $img) ?>" target="_blank">
                                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/gallery/' . $img) ?>" alt="" class="img-fluid rounded-3" style="cursor: zoom-in;">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <h4 class="mb-0">Technologies Used</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        $techs = json_decode($project['technologies'] ?? '[]', true) ?? [];
                        if (!empty($techs)): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($techs as $tech): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2"><?= e($tech) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No technologies listed.</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($project['client_name'] || $project['project_date']): ?>
                        <div class="card-header">
                            <h4 class="mb-0">Project Info</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <?php if ($project['client_name']): ?>
                                    <li class="mb-2"><strong>Client:</strong> <?= e($project['client_name']) ?></li>
                                <?php endif; ?>
                                <?php if ($project['project_date']): ?>
                                    <li class="mb-2"><strong>Date:</strong> <?= format_date($project['project_date'], 'F Y') ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="row mt-5 pt-5 border-top">
            <div class="col-12 text-center">
                <a href="/#projects" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Projects
                </a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>