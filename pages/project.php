<?php
$slug = trim(substr($route, strlen('project/')), '/');
$statement = $pdo->prepare('SELECT * FROM projects WHERE slug = ? LIMIT 1');
$statement->execute([$slug]);
$project = $statement->fetch();
if ($project === false) {
    http_response_code(404);
    $pageFile = __DIR__ . '/pages/404.php';
    return;
}
$pageTitle = $project['title'];
$pageDescription = tamim_excerpt($project['summary'], 160);
$technologies = tamim_json_array($project['technologies']);
$nextStatement = $pdo->prepare('SELECT title, slug FROM projects WHERE id > ? ORDER BY id LIMIT 1');
$nextStatement->execute([$project['id']]);
$nextProject = $nextStatement->fetch();
?>
<section class="project-hero section-soft"><div class="container"><a class="back-link" href="/projects">← All projects</a><div class="project-hero-grid"><div><p class="eyebrow">Case study</p><h1><?php echo tamim_e($project['title']); ?></h1><p class="page-hero-lead"><?php echo tamim_e($project['summary']); ?></p><div class="tag-row"><?php foreach ($technologies as $technology): ?><span class="tag"><?php echo tamim_e($technology); ?></span><?php endforeach; ?></div></div><div class="project-hero-art art-<?php echo (int) $project['id'] % 3 + 1; ?>" aria-hidden="true"><span><?php echo tamim_e(mb_strtoupper(mb_substr($project['title'], 0, 1))); ?></span><small><?php echo tamim_e($project['title']); ?></small></div></div></div></section>
<section class="section"><div class="container case-layout"><div class="case-copy"><p class="eyebrow">The story</p><h2><?php echo nl2br(tamim_e($project['description'])); ?></h2></div><aside class="case-facts"><div><span>Role</span><strong>Design &amp; development</strong></div><div><span>Focus</span><strong>Clarity, speed, care</strong></div><div><span>Stack</span><strong><?php echo tamim_e(implode(', ', $technologies)); ?></strong></div><div class="case-links"><?php if (tamim_url($project['demo_url']) !== '#'): ?><a class="button button-primary" href="<?php echo tamim_e(tamim_url($project['demo_url'])); ?>">Visit project ↗</a><?php endif; ?><?php if (tamim_url($project['source_url']) !== '#'): ?><a class="text-link" href="<?php echo tamim_e(tamim_url($project['source_url'])); ?>">View source →</a><?php endif; ?></div></aside></div></section>
<section class="section section-tint"><div class="container outcome-grid"><article><span>01</span><h3>Understand</h3><p>Start with the people, content and decisions that matter most.</p></article><article><span>02</span><h3>Simplify</h3><p>Remove noise so the useful path becomes easy to see.</p></article><article><span>03</span><h3>Refine</h3><p>Test the details until the experience feels calm and complete.</p></article></div></section>
<?php if ($nextProject): ?><section class="next-project"><div class="container"><span>Next project</span><a href="/project/<?php echo tamim_e($nextProject['slug']); ?>"><h2><?php echo tamim_e($nextProject['title']); ?></h2><b>↗</b></a></div></section><?php endif; ?>
