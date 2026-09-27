<?php
/**
 * Blog Post Details Page
 * Portfolio Website
 */

require_once __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h1>404 - Post Not Found</h1><a href="/Tamim/blog.php" class="btn btn-primary mt-3">Back to Blog</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$post = blog_post_by_slug($slug);
if (!$post) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h1>404 - Post Not Found</h1><a href="/Tamim/blog.php" class="btn btn-primary mt-3">Back to Blog</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Increment views
db_execute('UPDATE blog_posts SET views = views + 1 WHERE id = ?', [$post['id']]);

// Get related posts
$relatedPosts = db_all('SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.status = "published" AND bp.id != ? ORDER BY bp.published_at DESC LIMIT 3', [$post['id']]);

$pageTitle = $post['title'] . ' | ' . setting('site_name');
$pageDescription = $post['meta_description'] ?? $post['excerpt'] ?? setting('seo_description');
$pageUrl = current_url();
$pageImage = $post['featured_image'] ? setting('site_url', '/Tamim') . '/assets/uploads/blog/' . $post['featured_image'] : '';
$pageType = 'article';

require __DIR__ . '/includes/header.php';
?>

<!-- Blog Post -->
<article class="section" style="padding-top: 140px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/Tamim/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/Tamim/blog.php">Blog</a></li>
                        <?php if ($post['category_name']): ?>
                            <li class="breadcrumb-item"><a href="/Tamim/blog.php?category=<?= e($post['category_slug']) ?>"><?= e($post['category_name']) ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active" aria-current="page"><?= e($post['title']) ?></li>
                    </ol>
                </nav>
                
                <!-- Category & Date -->
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php if ($post['category_name']): ?>
                        <a href="/Tamim/blog.php?category=<?= e($post['category_slug']) ?>" class="badge bg-primary text-decoration-none"><?= e($post['category_name']) ?></a>
                    <?php endif; ?>
                    <span class="badge bg-secondary"><?= format_date($post['published_at'], 'F d, Y') ?></span>
                    <span class="badge bg-info"><?= $post['views'] ?? 0 ?> Views</span>
                </div>
                
                <!-- Title -->
                <h1 class="display-4 fw-bold mb-4"><?= e($post['title']) ?></h1>
                
                <!-- Author -->
                <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 16px; font-weight: 700;">
                        <?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-medium"><?= e($post['author_name'] ?? 'Admin') ?></div>
                        <small class="text-muted">Published on <?= format_date($post['published_at'], 'F d, Y') ?></small>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <?php if ($post['featured_image']): ?>
                    <div class="mb-5">
                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/blog/' . $post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="img-fluid rounded-3 shadow">
                    </div>
                <?php endif; ?>
                
                <!-- Content -->
                <div class="blog-content" style="font-size: 17px; line-height: 1.8; color: var(--text-color);">
                    <?php if ($post['excerpt']): ?>
                        <div class="lead text-muted mb-5 p-4 bg-light rounded-3 border-start border-4 border-primary">
                            <?= e($post['excerpt']) ?>
                        </div>
                    <?php endif; ?>
                    <?= $post['content'] ?>
                </div>
                
                <!-- Tags -->
                <?php 
                $tags = json_decode($post['tags'] ?? '[]', true) ?? [];
                if (!empty($tags)): ?>
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="text-muted small">Tags:</span>
                            <?php foreach ($tags as $tag): ?>
                                <a href="/Tamim/blog.php?tag=<?= urlencode($tag) ?>" class="badge bg-light text-dark text-decoration-none border px-3 py-2">#<?= e($tag) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Share -->
                <div class="mt-5 pt-4 border-top">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-medium">Share:</span>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode($pageUrl) ?>" target="_blank" class="social-link" style="width: 40px; height: 40px; font-size: 14px; background: #1da1f2; color: white; border-color: #1da1f2;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank" class="social-link" style="width: 40px; height: 40px; font-size: 14px; background: #4267b2; color: white; border-color: #4267b2;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($pageUrl) ?>" target="_blank" class="social-link" style="width: 40px; height: 40px; font-size: 14px; background: #0077b5; color: white; border-color: #0077b5;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="mailto:?subject=<?= urlencode($post['title']) ?>&body=<?= urlencode('Check out this article: ' . $pageUrl) ?>" class="social-link" style="width: 40px; height: 40px; font-size: 14px;">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                    <div class="mt-5">
                        <hr class="my-4">
                        <h3 class="h4 mb-4">Related Articles</h3>
                        <div class="row g-4">
                            <?php foreach ($relatedPosts as $related): ?>
                                <div class="col-md-4">
                                    <article class="card h-100 border-0 shadow-sm overflow-hidden">
                                        <?php if ($related['featured_image']): ?>
                                            <a href="/Tamim/blog/<?= e($related['slug']) ?>">
                                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/blog/' . $related['featured_image']) ?>" alt="" class="card-img-top" style="height: 150px; object-fit: cover;">
                                            </a>
                                        <?php else: ?>
                                            <a href="/Tamim/blog/<?= e($related['slug']) ?>">
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                                    <i class="fas fa-blog fa-2x text-muted"></i>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="/Tamim/blog/<?= e($related['slug']) ?>" class="text-dark text-decoration-none"><?= e($related['title']) ?></a>
                                            </h5>
                                            <small class="text-muted"><?= format_date($related['published_at']) ?></small>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Navigation -->
                <div class="mt-5 pt-4 border-top">
                    <a href="/Tamim/blog.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Blog
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>