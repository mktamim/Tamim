<?php
$skills = $pdo->query('SELECT * FROM skills ORDER BY sort_order, name')->fetchAll();
$categories = [];
foreach ($skills as $skill) {
    $categories[$skill['category']][] = $skill;
}
?>
<section class="page-hero section-soft"><div class="container narrow"><p class="eyebrow">What I bring</p><h1>Skills that turn ideas into <em>working products</em>.</h1><p class="page-hero-lead">A practical toolkit for building reliable, responsive and maintainable web experiences.</p></div></section>
<section class="section"><div class="container skills-layout"><div class="skills-intro"><p class="eyebrow">Core toolkit</p><h2>Comfortable across the stack, focused on the outcome.</h2><p class="muted">I choose tools according to the problem and keep the implementation as simple as the product allows.</p><a class="button button-secondary" href="/contact">Talk about your project</a></div><div class="skill-groups">
<?php foreach ($categories as $category => $categorySkills): ?>
    <section class="skill-group"><div class="skill-group-heading"><h3><?php echo tamim_e($category); ?></h3><span><?php echo count($categorySkills); ?> skills</span></div>
    <?php foreach ($categorySkills as $skill): ?><div class="skill-row"><div><strong><?php echo tamim_e($skill['name']); ?></strong><span><?php echo (int) $skill['level']; ?>%</span></div><div class="progress-track"><span style="width:<?php echo (int) $skill['level']; ?>%"></span></div></div><?php endforeach; ?>
    </section>
<?php endforeach; ?>
</div></div></section>
<section class="section section-tint"><div class="container process-strip"><div><span>01</span><h3>Discover</h3><p>Understand the people, goals and constraints.</p></div><div><span>02</span><h3>Define</h3><p>Turn ambiguity into a clear, useful plan.</p></div><div><span>03</span><h3>Deliver</h3><p>Build, test and refine with care.</p></div></div></section>
