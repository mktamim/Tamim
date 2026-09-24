<?php
$posts = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC")->fetchAll();
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">Journal</p><h1>Notes on building for the <em>web</em>.</h1><p class="page-hero-lead">Occasional thoughts about design, development and the decisions behind useful products.</p></div></section>
<section class="section"><div class="container posts-grid">
<?php foreach ($posts as $post): ?>
<article class="post-card"><div class="post-card-art art-<?php echo (int) $post['id'] % 3 + 1; ?>"><span><?php echo tamim_e(date('d', strtotime($post['published_at']))); ?></span><small><?php echo tamim_e(date('M Y', strtotime($post['published_at']))); ?></small></div><div class="post-card-body"><p class="eyebrow">Journal</p><h2><a href="/blog/<?php echo tamim_e($post['slug']); ?>"><?php echo tamim_e($post['title']); ?></a></h2><p><?php echo tamim_e(tamim_excerpt($post['excerpt'], 140)); ?></p><a class="text-link" href="/blog/<?php echo tamim_e($post['slug']); ?>">Read article <span>→</span></a></div></article>
<?php endforeach; ?>
</div></section>
