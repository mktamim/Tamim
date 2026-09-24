<?php
$experiences = $pdo->query('SELECT * FROM experience ORDER BY sort_order, start_date DESC')->fetchAll();
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">Career journey</p><h1>Experience shaped by <em>curiosity</em>.</h1><p class="page-hero-lead">A path through products, teams and problems that keep getting more interesting.</p></div></section>
<section class="section"><div class="container timeline">
<?php foreach ($experiences as $experience): ?>
<article class="timeline-item"><div class="timeline-dot"></div><div class="timeline-date"><strong><?php echo tamim_format_date($experience['start_date'], 'M Y'); ?></strong><span><?php echo $experience['current_job'] ? 'Present' : tamim_format_date($experience['end_date'], 'M Y'); ?></span></div><div class="timeline-content"><p class="eyebrow"><?php echo tamim_e($experience['company']); ?><?php echo $experience['location'] ? ' · ' . tamim_e($experience['location']) : ''; ?></p><h2><?php echo tamim_e($experience['title']); ?></h2><p><?php echo tamim_e($experience['description']); ?></p></div></article>
<?php endforeach; ?>
</div></section>
<section class="cta-section cta-section-soft"><div class="container cta-inner"><div><p class="eyebrow">Next chapter</p><h2>Let us make something <em>together</em>.</h2></div><a class="button button-primary" href="/contact">Get in touch <span>↗</span></a></div></section>
