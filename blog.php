<?php
/**
 * Blog Listing Page
 * Portfolio Website
 */

require_once __DIR__ . '/includes/bootstrap.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

$category = $_GET['category'] ?? '';
$where = 'WHERE bp.status = "published"';
$params = [];

if ($category) {
    $where .= ' AND bc.slug = ?';
    $params[] = $category;
}

$total = db_one('SELECT COUNT(*) as c FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id ' . $where, $params)['c'] ?? 0;
$posts = db_all('SELECT bp.*, bc.name as category_name, bc.slug as category_slug, a.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN admins a ON bp.author_id = a.id ' . $where . ' ORDER BY bp.published_at DESC LIMIT ? OFFSET ?', array_merge($params, [$perPage, $offset]));

$categories = blog_categories();

$pagination = paginate($total, $perPage, $page, '/Tamim/blog.php' . ($category ? '?category=' . $category : ''));

$pageTitle = 'Blog | ' . setting('site_name');
$pageDescription = 'Latest articles and tutorials on web development, PHP, Laravel, and more.';
$pageKeywords = 'blog, web development, PHP, Laravel, tutorials, articles';

require __DIR__ . '/includes/header.php';
?>

<!-- Blog Hero -->
<section class="section" style="padding-top: 140px; padding-bottom: 60px; background: var(--light-color);">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <span class="section-label">Blog</span>
                <h1 class="display-4 fw-bold mb-4">Latest Articles</h1>
                <p class="lead text-muted">Insights, tutorials, and thoughts on web development, PHP, Laravel, and modern technologies.</p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-3 py-2 <?= empty($category) ? 'active' : '' ?>">
                                <a href="/Tamim/blog.php" class="text-decoration-none d-block">All Posts</a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li class="list-group-item px-3 py-2 <?= $category === $cat['slug'] ? 'active' : '' ?>">
                                    <a href="/Tamim/blog.php?category=<?= e($cat['slug']) ?>" class="text-decoration-none d-block">
                                        <?= e($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Posts -->
            <div class="col-lg-9">
                <?php if (empty($posts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Posts Found</h4>
                        <p class="text-muted"><?= $category ? 'No posts in this category yet.' : 'Be the first to write a post!' ?></p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $index => $post): ?>
                            <div class="col-md-6 fade-in" style="transition-delay: <?= $index * 0.1 ?>s;">
                                <article class="card h-100 border-0 shadow-sm overflow-hidden">
                                    <?php if ($post['featured_image']): ?>
                                        <a href="/Tamim/blog/<?= e($post['slug']) ?>">
                                            <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/blog/' . $post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <a href="/Tamim/blog/<?= e($post['slug']) ?>">
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-blog fa-3x text-muted"></i>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-2">
                                            <?php if ($post['category_name']): ?>
                                                <a href="/Tamim/blog.php?category=<?= e($post['category_slug']) ?>" class="badge bg-primary text-decoration-none"><?= e($post['category_name']) ?></a>
                                            <?php endif; ?>
                                            <span class="badge bg-secondary ms-1"><?= format_date($post['published_at']) ?></span>
                                        </div>
                                        
                                        <h3 class="card-title h5 mb-3">
                                            <a href="/Tamim/blog/<?= e($post['slug']) ?>" class="text-dark text-decoration-none"><?= e($post['title']) ?></a>
                                        </h3>
                                        
                                        <p class="card-text text-muted small flex-grow-1"><?= e(mb_strimwidth($post['excerpt'] ?? $post['content'] ?? '', 0, 120, '...')) ?></p>
                                        
                                        <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; font-weight: 700;">
                                                    <?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?>
                                                </div>
                                                <small class="text-muted"><?= e($post['author_name'] ?? 'Admin') ?></small>
                                            </div>
                                            <a href="/Tamim/blog/<?= e($post['slug']) ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Blog pagination" class="mt-5">
                            <?= render_pagination($pagination) ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>