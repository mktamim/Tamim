<?php
$skills = $pdo->query('SELECT * FROM skills ORDER BY sort_order, name')->fetchAll();
$services = $pdo->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();
$projects = $pdo->query('SELECT * FROM projects WHERE featured = 1 ORDER BY sort_order LIMIT 3')->fetchAll();
$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order LIMIT 3')->fetchAll();
$heroTitle = tamim_setting('hero_title', 'Building digital products with purpose.');
$heroSubtitle = tamim_setting('hero_subtitle', '');
$heroEmail = tamim_setting('hero_email', 'hello@tamim.dev');
$heroLocation = tamim_setting('hero_location', 'Bangladesh');
$ctaLabel = tamim_setting('hero_cta_label', 'View my work');
$ctaUrl = tamim_url(tamim_setting('hero_cta_url', '/projects'));
?>
<section class="hero section-soft">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow"><span class="status-dot"></span>Available for selected projects</p>
            <h1><?php echo tamim_e($heroTitle); ?></h1>
            <p class="hero-lead"><?php echo tamim_e($heroSubtitle); ?></p>
            <div class="hero-actions"><a class="button button-primary" href="<?php echo tamim_e($ctaUrl); ?>">View my work</a><a class="button button-secondary" href="/contact">Start a conversation</a></div>
            <div class="hero-meta"><span><?php echo tamim_e($heroLocation); ?></span><span>Responsive by default</span><span>Performance minded</span></div>
        </div>
        <div class="hero-visual" aria-hidden="true">
            <img src="/assets/images/hero-bg.svg" alt="" class="hero-bg-img">
            <div class="visual-window">
                <div class="window-bar"><span></span><span></span><span></span><strong>tamim.dev</strong></div>
                <div class="window-content">
                    <div class="code-line"><i></i><i></i><i></i></div>
                    <div class="code-line short"><i></i><i></i></div>
                    <div class="visual-card"><span class="visual-icon">T</span><div><strong>Make it useful.</strong><small>Then make it beautiful.</small></div></div>
                    <div class="visual-card reverse"><span class="visual-icon icon-secondary">&lt;/&gt;</span><div><strong>Build with care.</strong><small>Ship with confidence.</small></div></div>
                </div>
            </div>
            <div class="floating-note note-one"><span>01</span>Strategy</div>
            <div class="floating-note note-two"><span>02</span>Design</div>
            <div class="floating-note note-three"><span>03</span>Development</div>
        </div>
    </div>
</section>

<section class="section" id="about-preview">
    <div class="container section-intro split-intro">
        <div><p class="eyebrow">A little about me</p><h2>Good products begin with <em>clear thinking</em>.</h2></div>
        <div><p class="lead"><?php echo tamim_e(tamim_setting('about_lead', '')); ?></p><p class="muted"><?php echo tamim_e(tamim_setting('about_description', '')); ?></p><a class="text-link" href="/about">More about me <span>→</span></a></div>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <div class="section-intro"><div><p class="eyebrow">What I do</p><h2>Services that move a product <em>forward</em>.</h2></div><p class="section-note">From first sketch to final launch, every engagement is shaped around the people who will use it.</p></div>
        <div class="service-grid">
            <?php foreach ($services as $service): ?>
            <article class="service-card">
                <div class="service-icon"><?php echo tamim_e($service['icon'] === 'code' ? '</>' : ($service['icon'] === 'layout' ? '▣' : ($service['icon'] === 'dashboard' ? '◫' : '✦'))); ?></div>
                <h3><?php echo tamim_e($service['title']); ?></h3>
                <p><?php echo tamim_e($service['description']); ?></p>
                <a class="text-link" href="/contact">Discuss this service <span>→</span></a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="work">
    <div class="container">
        <div class="section-intro"><div><p class="eyebrow">Selected work</p><h2>Projects with a <em>point of view</em>.</h2></div><a class="text-link" href="/projects">View all projects <span>→</span></a></div>
        <div class="project-grid project-grid-home">
            <?php foreach ($projects as $project): ?>
            <a class="project-card" href="/project/<?php echo tamim_e($project['slug']); ?>">
                <div class="project-art art-<?php echo (int) $project['id'] % 3 + 1; ?>"><span><?php echo tamim_e(mb_strtoupper(mb_substr($project['title'], 0, 1))); ?></span><small><?php echo tamim_e($project['title']); ?></small></div>
                <div class="project-card-body"><div class="project-card-top"><h3><?php echo tamim_e($project['title']); ?></h3><span class="arrow-circle">↗</span></div><p><?php echo tamim_e(tamim_excerpt($project['summary'], 120)); ?></p><div class="tag-row"><?php foreach (array_slice(tamim_json_array($project['technologies']), 0, 3) as $technology): ?><span class="tag"><?php echo tamim_e($technology); ?></span><?php endforeach; ?></div></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="section-intro light"><div><p class="eyebrow">Capabilities</p><h2>Tools I reach for <em>most often</em>.</h2></div><a class="text-link light-link" href="/skills">See all skills <span>→</span></a></div>
        <div class="skill-cloud">
            <?php foreach (array_slice($skills, 0, 8) as $skill): ?><span class="skill-pill"><?php echo tamim_e($skill['name']); ?><small><?php echo (int) $skill['level']; ?>%</small></span><?php endforeach; ?>
        </div>
        <div class="quote-row"><span class="quote-mark">“</span><blockquote>The best interfaces feel inevitable: useful, legible and quietly confident.</blockquote><span class="quote-author">— Tamim</span></div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-intro"><div><p class="eyebrow">Kind words</p><h2>People make the <em>work meaningful</em>.</h2></div><a class="text-link" href="/testimonials">Read more testimonials <span>→</span></a></div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
            <article class="testimonial-card"><div class="testimonial-quote">“</div><p><?php echo tamim_e($testimonial['message']); ?></p><div class="person-row"><span class="avatar avatar-<?php echo (int) $testimonial['id'] % 3 + 1; ?>"><?php echo tamim_e(mb_strtoupper(mb_substr($testimonial['name'], 0, 1))); ?></span><div><strong><?php echo tamim_e($testimonial['name']); ?></strong><small><?php echo tamim_e($testimonial['role']); ?></small></div></div></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container cta-inner"><div><p class="eyebrow">Have something to build?</p><h2>Let us make it <em>real</em>.</h2></div><a class="button button-light" href="mailto:<?php echo tamim_e($heroEmail); ?>">hello@tamim.dev <span>↗</span></a></div>
</section>
