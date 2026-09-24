<?php
$projects = $pdo->query('SELECT * FROM projects ORDER BY featured DESC, sort_order, created_at DESC')->fetchAll();
$projectImages = [1 => '/assets/images/project-1.svg', 2 => '/assets/images/project-2.svg', 3 => '/assets/images/project-3.svg'];
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">Selected work</p><h1>Projects built with <em>intention</em>.</h1><p class="page-hero-lead">A few examples of products, interfaces and systems shaped around real people and practical goals.</p></div></section>
<section class="section"><div class="container project-index">
<?php foreach ($projects as $project): ?>
<?php $imgIdx = (int) $project['id'] % 3 + 1; ?>
<article class="project-index-item"><a class="project-index-art" href="/project <?php echo tamim_e($project['slug']); ?>" style="background-image: url('<?php echo tamim_e($projectImages[$imgIdx]); ?>'); background-size: cover; background-position: center;"><span><?php echo tamim_e(mb_strtoupper(mb_substr($project['title'], 0, 1))); ?></span><small><?php echo tamim_e($project['title']); ?></small></a><div class="project-index-copy"><div class="project-index-meta"><span><?php echo (int) $project['featured'] ? 'Featured' : 'Project'; ?></span><span><?php echo tamim_e(date('Y', strtotime($project['created_at']))); ?></span></div><h2><a href="/project <?php echo tamim_e($project['slug']); ?>"><?php echo tamim_e($project['title']); ?></a></h2><p><?php echo tamim_e(tamim_excerpt($project['summary'], 160)); ?></p><div class="tag-row"><?php foreach (tamim_json_array($project['technologies']) as $technology): ?><span class="tag"><?php echo tamim_e($technology); ?></span><?php endforeach; ?></div><a class="text-link" href="/project <?php echo tamim_e($project['slug']); ?>">Read case study <span>→</span></a></div></article>
<?php endforeach; ?>
</div></section>
