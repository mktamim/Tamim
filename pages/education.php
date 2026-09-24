<?php
$educationItems = $pdo->query('SELECT * FROM education ORDER BY sort_order, graduation_year DESC')->fetchAll();
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">Learning path</p><h1>Education is a <em>practice</em>, not a destination.</h1><p class="page-hero-lead">Formal study, independent exploration and the daily habit of making things better.</p></div></section>
<section class="section"><div class="container education-grid">
<?php foreach ($educationItems as $item): ?>
<article class="education-card"><div class="education-year"><span><?php echo tamim_e((string) $item['graduation_year']); ?></span></div><div><p class="eyebrow"><?php echo tamim_e($item['institution']); ?></p><h2><?php echo tamim_e($item['degree']); ?><?php echo $item['field_of_study'] ? ' <span>in ' . tamim_e($item['field_of_study']) . '</span>' : ''; ?></h2><p><?php echo tamim_e($item['description']); ?></p></div></article>
<?php endforeach; ?>
</div></section>
<section class="section section-tint"><div class="container learning-note"><div class="learning-mark">∞</div><div><p class="eyebrow">Always learning</p><h2>The web changes. Good developers keep <em>paying attention</em>.</h2><p class="muted">I stay close to the platform, the people using it and the small details that make a product feel considered.</p></div></div></section>
