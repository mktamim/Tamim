<?php
$slug = trim(substr($route, strlen('blog/')), '/');
$statement = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published' LIMIT 1");
$statement->execute([$slug]);
$post = $statement->fetch();
if ($post === false) {
    http_response_code(404);
    $pageFile = __DIR__ . '/pages/404.php';
    return;
}
$pageTitle = $post['title'];
$pageDescription = tamim_excerpt($post['excerpt'], 160);
$published = tamim_format_date($post['published_at'], 'F d, Y');
?>
<section class="article-hero section-soft"><div class="container article-hero-inner"><a class="back-link" href="/blog">← Journal</a><p class="eyebrow">Journal · <?php echo tamim_e($published); ?></p><h1><?php echo tamim_e($post['title']); ?></h1><p class="page-hero-lead"><?php echo tamim_e($post['excerpt']); ?></p><div class="article-byline"><span class="avatar avatar-1">T</span><div><strong>Tamim</strong><small>Web developer &amp; writer</small></div></div></div></section>
<article class="article-body"><div class="container article-layout"><div class="article-content"><?php echo nl2br(tamim_e($post['content'])); ?></div><aside class="article-aside"><div><span>Share this note</span><div class="share-row"><a href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode($post['title']); ?>" target="_blank" rel="noopener noreferrer">X</a><a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode(tamim_url($_SERVER['REQUEST_URI'])); ?>" target="_blank" rel="noopener noreferrer">in</a></div></div><a class="button button-secondary article-cta" href="/blog">Back to journal</a></aside></div></article>
<section class="section section-tint"><div class="container article-end"><span>Thanks for reading.</span><a class="text-link" href="/contact">Let us talk about your project <span>→</span></a></div></section>
