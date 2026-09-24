<?php
$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order')->fetchAll();
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">Testimonials</p><h1>Work is better when people feel <em>heard</em>.</h1><p class="page-hero-lead">A few notes from clients and collaborators who trusted the process.</p></div></section>
<section class="section"><div class="container testimonials-index">
<?php foreach ($testimonials as $testimonial): ?>
<article class="testimonial-large"><div class="testimonial-large-mark">“</div><div><p><?php echo tamim_e($testimonial['message']); ?></p><div class="person-row"><span class="avatar avatar-<?php echo (int) $testimonial['id'] % 3 + 1; ?>"><?php echo tamim_e(mb_strtoupper(mb_substr($testimonial['name'], 0, 1))); ?></span><div><strong><?php echo tamim_e($testimonial['name']); ?></strong><small><?php echo tamim_e($testimonial['role']); ?></small></div></div></div></article>
<?php endforeach; ?>
</div></section>
