<?php
$services = $pdo->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">How I can help</p><h1>Web services for teams who value <em>clarity</em>.</h1><p class="page-hero-lead">Flexible support for new products, evolving websites and the details that make a launch feel polished.</p></div></section>
<section class="section"><div class="container service-list">
<?php foreach ($services as $index => $service): ?>
<article class="service-list-item"><div class="service-number">0<?php echo (int) $index + 1; ?></div><div class="service-list-copy"><p class="eyebrow">Service <?php echo (int) $index + 1; ?></p><h2><?php echo tamim_e($service['title']); ?></h2><p><?php echo tamim_e($service['description']); ?></p><a class="text-link" href="/contact">Start a conversation <span>→</span></a></div><div class="service-list-mark" aria-hidden="true"><?php echo tamim_e($service['icon'] === 'code' ? '</>' : ($service['icon'] === 'layout' ? '▣' : ($service['icon'] === 'dashboard' ? '◫' : '✦'))); ?></div></article>
<?php endforeach; ?>
</div></section>
<section class="cta-section"><div class="container cta-inner"><div><p class="eyebrow">Not sure where to start?</p><h2>A good conversation is a good <em>first step</em>.</h2></div><a class="button button-light" href="/contact">Tell me about it <span>↗</span></a></div></section>
