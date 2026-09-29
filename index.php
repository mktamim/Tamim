<?php
/**
 * Homepage - Portfolio Website
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = setting('site_name', 'My Portfolio') . ' | ' . setting('developer_title', 'Full Stack Web Developer');
$pageDescription = setting('seo_description', 'Professional web developer portfolio');
$pageKeywords = setting('seo_keywords', 'web developer, PHP, Laravel');

require __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section id="home" class="section hero-section">
    <div class="hero-bg-animation">
        <div class="code-rain" id="codeRain"></div>
        <div class="hero-gradient-orb orb-1"></div>
        <div class="hero-gradient-orb orb-2"></div>
        <div class="hero-gradient-orb orb-3"></div>
        <div class="hero-grid-pattern"></div>
        <div class="floating-elements">
            <div class="float-item float-1"><i class="fas fa-code"></i></div>
            <div class="float-item float-2"><i class="fas fa-database"></i></div>
            <div class="float-item float-3"><i class="fas fa-cloud"></i></div>
            <div class="float-item float-4"><i class="fas fa-terminal"></i></div>
            <div class="float-item float-5"><i class="fas fa-layer-group"></i></div>
            <div class="float-item float-6"><i class="fas fa-microchip"></i></div>
        </div>
    </div>
    
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-7 hero-content-wrapper fade-in-up">
                <div class="hero-badge">
                    <span class="badge-dot"></span>
                    <span><?= e(setting('hero_badge', 'Available for freelance projects')) ?></span>
                </div>
                
                <h1 class="hero-name">
                    <span class="greeting">Hello, I'm</span>
                    <span class="name-typing">Md Tamim Iqbal</span>
                </h1>
                
                <div class="hero-title-wrapper">
                    <h2 class="hero-title"><?= e(setting('developer_title', 'Full Stack Web Developer')) ?></h2>
                    <div class="title-underline"></div>
                </div>
                
                <p class="hero-description"><?= e(setting('hero_subtitle', 'I build fast, scalable and user-friendly websites and web applications for businesses and individuals.')) ?></p>
                
                <div class="hero-buttons">
                    <a href="#projects" class="btn btn-primary btn-hero">
                        <span class="btn-text"><i class="fas fa-folder-open me-2"></i><?= e(setting('hero_cta_text', 'View My Work')) ?></span>
                        <span class="btn-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                    <a href="#contact" class="btn btn-secondary btn-hero">
                        <span class="btn-text"><i class="fas fa-paper-plane me-2"></i><?= e(setting('hero_cta2_text', 'Hire Me')) ?></span>
                    </a>
                </div>
                
                <div class="hero-social">
                    <?php foreach (social_links() as $social): ?>
                        <a href="<?= e($social['url']) ?>" class="social-link-modern" target="_blank" rel="noopener" title="<?= e($social['label']) ?>">
                            <i class="<?= e($social['icon_class']) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="hero-tech-stack">
                    <span class="tech-label">Tech Stack</span>
                    <div class="tech-icons">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" title="PHP">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg" alt="Laravel" title="Laravel">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript" title="JavaScript">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="MySQL" title="MySQL">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" alt="Bootstrap" title="Bootstrap">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg" alt="Git" title="Git">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" alt="Docker" title="Docker">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg" alt="VS Code" title="VS Code">
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 hero-visual-wrapper fade-in-up" style="transition-delay: 0.2s;">
                <div class="hero-image-container transparent">
                    <?php if (setting('profile_image')): ?>
                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . setting('profile_image')) ?>" alt="<?= e(setting('developer_name')) ?>" class="hero-profile-img">
                    <?php else: ?>
                        <div class="hero-profile-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
            <span>Scroll to explore</span>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">About Me</span>
            <h2 class="section-title">Get to Know Me</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Learn more about my background, experience, and what drives me as a developer.</p>
        </div>
        
        <div class="row align-items-start">
            <div class="col-lg-6 fade-in">
                <div class="about-image position-relative text-center">
                    <?php if (setting('profile_image')): ?>
                        <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . setting('profile_image')) ?>" alt="<?= e(setting('developer_name')) ?>" class="img-fluid mx-auto">
                    <?php else: ?>
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mx-auto" style="min-height: 400px; max-width: 100%;">
                            <i class="fas fa-user fa-5x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in" style="transition-delay: 0.1s;">
                <div class="about-text">
                    <h3>Passionate Developer Crafting Digital Solutions</h3>
                    <p><?= e(setting('about_short_intro', 'Passionate web developer with 5+ years of experience building modern web applications.')) ?></p>
                    <p><?= e(setting('about_detailed_bio', 'I am a dedicated full-stack web developer with expertise in PHP, Laravel, JavaScript, and modern web technologies. I love creating clean, efficient, and scalable solutions that solve real-world problems.')) ?></p>
                    
                    <div class="about-stats-row mt-4">
                        <div class="stat-card">
                            <div class="stat-number"><?= e(setting('stat_experience', '5+')) ?></div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= e(setting('stat_projects', '100+')) ?></div>
                            <div class="stat-label">Projects Completed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= e(setting('stat_clients', '50+')) ?></div>
                            <div class="stat-label">Happy Clients</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section id="skills" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Skills</span>
            <h2 class="section-title">Technical Expertise</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Technologies and tools I work with to build exceptional web applications.</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $allSkills = skills();
            if (empty($allSkills)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-code-branch fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No skills configured yet. Add skills from the admin panel.</p>
                </div>
            <?php else: 
                foreach ($allSkills as $index => $skill): 
            ?>
                <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: <?= $index * 0.1 ?>s;">
                    <div class="skill-card">
                        <div class="skill-icon">
                            <?php if ($skill['icon_type'] === 'image' && $skill['icon_image']): ?>
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/skills/' . $skill['icon_image']) ?>" alt="" style="width: 48px; height: 48px;">
                            <?php else: ?>
                                <i class="<?= e($skill['icon_class'] ?? 'fas fa-code') ?>"></i>
                            <?php endif; ?>
                        </div>
                        <h4><?= e($skill['name']) ?></h4>
                        <?php if ($skill['description']): ?>
                            <p><?= e($skill['description']) ?></p>
                        <?php endif; ?>
                        <div class="skill-progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="<?= (int)$skill['percentage'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Services</span>
            <h2 class="section-title">What I Offer</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Professional services to help bring your digital ideas to life.</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $allServices = services();
            if (empty($allServices)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No services configured yet. Add services from the admin panel.</p>
                </div>
            <?php else: 
                foreach ($allServices as $index => $service): 
            ?>
                <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: <?= $index * 0.1 ?>s;">
                    <div class="service-card">
                        <div class="service-icon">
                            <?php if ($service['icon_type'] === 'image' && $service['icon_image']): ?>
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/services/' . $service['icon_image']) ?>" alt="" style="width: 48px; height: 48px;">
                            <?php else: ?>
                                <i class="<?= e($service['icon_class'] ?? 'fas fa-briefcase') ?>"></i>
                            <?php endif; ?>
                        </div>
                        <h4><?= e($service['title']) ?></h4>
                        <p><?= e($service['short_description'] ?? 'Professional service description.') ?></p>
                        <?php if ($service['features']): 
                            $features = json_decode($service['features'], true) ?? [];
                            if (!empty($features)): ?>
                                <ul class="service-features">
                                    <?php foreach ($features as $feature): ?>
                                        <li><?= e($feature) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; 
                        endif; ?>
                    </div>
                </div>
            <?php endforeach; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section id="projects" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Portfolio</span>
            <h2 class="section-title">Selected Work</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">A showcase of projects I've worked on, demonstrating various technologies and solutions.</p>
        </div>
        
        <!-- Category Filters -->
        <div class="project-filters fade-in">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php 
            $categories = project_categories();
            foreach ($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></button>
            <?php endforeach; ?>
        </div>
        
        <!-- Projects Grid -->
        <div class="row g-4" id="projectsGrid">
            <?php 
            $allProjects = projects(['limit' => 8]);
            if (empty($allProjects)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No projects yet. Add projects from the admin panel.</p>
                </div>
            <?php else: 
                foreach ($allProjects as $index => $project): 
            ?>
                <div class="col-lg-4 col-md-6 project-card fade-in" data-category="<?= e($project['category_slug'] ?? 'all') ?>" style="transition-delay: <?= $index * 0.1 ?>s;">
                    <div class="project-image">
                        <?php if ($project['cover_image']): ?>
                            <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/projects/' . $project['cover_image']) ?>" alt="<?= e($project['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="bg-light h-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-folder-open fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="project-overlay">
                            <div class="project-links">
                                <a href="/project/<?= e($project['slug']) ?>" class="project-link" title="View Project">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($project['live_url']): ?>
                                    <a href="<?= e($project['live_url']) ?>" class="project-link" target="_blank" title="Live Demo">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="project-content">
                        <span class="project-category"><?= e($project['category_name'] ?? 'General') ?></span>
                        <h4><?= e($project['title']) ?></h4>
                        <p><?= e(mb_strimwidth($project['short_description'] ?? '', 0, 100, '...')) ?></p>
                        <div class="project-tech">
                            <?php 
                            $techs = json_decode($project['technologies'] ?? '[]', true) ?? [];
                            foreach (array_slice($techs, 0, 4) as $tech): ?>
                                <span><?= e($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; 
            endif; ?>
        </div>
        
        <div class="text-center mt-5 fade-in">
            <a href="/projects.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-folder-open me-2"></i>View All Projects
            </a>
        </div>
    </div>
</section>

<!-- Experience Section -->
<section id="experience" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Experience</span>
            <h2 class="section-title">Professional Journey</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">My career path and the experiences that shaped my expertise.</p>
        </div>
        
        <div class="timeline">
            <?php 
            $experiences = experiences();
            if (empty($experiences)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No experience entries yet.</p>
                </div>
            <?php else: 
                foreach ($experiences as $index => $exp): 
            ?>
                <div class="timeline-item fade-in" style="transition-delay: <?= $index * 0.1 ?>s;">
                    <div class="timeline-marker">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <span class="timeline-company"><?= e($exp['company_name']) ?></span>
                            <span class="timeline-period">
                                <?= format_date($exp['start_date'], 'M Y') ?> - 
                                <?= $exp['is_current'] ? 'Present' : format_date($exp['end_date'], 'M Y') ?>
                            </span>
                        </div>
                        <div class="timeline-position"><?= e($exp['position']) ?></div>
                        <?php if ($exp['location']): ?>
                            <div class="timeline-location">
                                <i class="fas fa-map-marker-alt"></i><?= e($exp['location']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($exp['description']): ?>
                            <p><?= e($exp['description']) ?></p>
                        <?php endif; ?>
                        <?php 
                        $techs = json_decode($exp['technologies'] ?? '[]', true) ?? [];
                        if (!empty($techs)): ?>
                            <div class="timeline-tech">
                                <?php foreach ($techs as $tech): ?>
                                    <span><?= e($tech) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Education Section -->
<section id="education" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Education</span>
            <h2 class="section-title">Academic Background</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">My educational qualifications and continuous learning journey.</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $educations = educations();
            if (empty($educations)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No education entries yet.</p>
                </div>
            <?php else: 
                foreach ($educations as $index => $edu): 
            ?>
                <div class="col-lg-6 fade-in" style="transition-delay: <?= $index * 0.1 ?>s;">
                    <div class="education-card">
                        <div class="education-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4><?= e($edu['degree']) ?></h4>
                        <p class="education-institution"><?= e($edu['institution']) ?></p>
                        <?php if ($edu['subject']): ?>
                            <p class="education-subject"><?= e($edu['subject']) ?></p>
                        <?php endif; ?>
                        <p class="education-period">
                            <i class="fas fa-calendar"></i>
                            <?= $edu['start_year'] ?> - <?= $edu['is_current'] ? 'Present' : ($edu['end_year'] ?? 'N/A') ?>
                            <?php if ($edu['location']): ?>
                                | <i class="fas fa-map-marker-alt"></i><?= e($edu['location']) ?>
                            <?php endif; ?>
                        </p>
                        <?php if ($edu['grade']): ?>
                            <span class="education-grade"><i class="fas fa-award"></i> <?= e($edu['grade']) ?></span>
                        <?php endif; ?>
                        <?php if ($edu['description']): ?>
                            <p class="mt-3" style="color: var(--text-muted); font-size: 14px;"><?= e($edu['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Testimonials</span>
            <h2 class="section-title">Client Feedback</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">What my clients have to say about working with me.</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $testimonials = testimonials();
            if (empty($testimonials)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No testimonials yet.</p>
                </div>
            <?php else: 
                foreach ($testimonials as $index => $testimonial): 
            ?>
                <div class="col-lg-4 fade-in" style="transition-delay: <?= $index * 0.1 ?>s;">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $testimonial['rating'] ? '' : 'text-muted' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text">"<?= e($testimonial['review']) ?>"</p>
                        <div class="testimonial-author">
                            <?php if ($testimonial['client_image']): ?>
                                <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/testimonials/' . $testimonial['client_image']) ?>" alt="<?= e($testimonial['client_name']) ?>" class="testimonial-avatar">
                            <?php else: ?>
                                <div class="testimonial-avatar bg-light d-flex align-items-center justify-content-center" style="font-size: 20px; color: var(--text-muted);">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <div class="testimonial-author-info">
                                <h5><?= e($testimonial['client_name']) ?></h5>
                                <span><?= e($testimonial['client_designation'] ?? '') ?> <?= $testimonial['client_company'] ? 'at ' . e($testimonial['client_company']) : '' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section">
    <div class="container">
        <div class="section-header fade-in" style="text-align: left;">
            <span class="section-label">Contact</span>
            <h2 class="section-title">Let's Work Together</h2>
            <div class="section-divider" style="margin-left: 0; margin-right: 0;"></div>
            <p class="section-subtitle" style="max-width: none;">Have a project in mind? I'd love to hear about it. Send me a message and let's discuss.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-5 fade-in">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Email</h5>
                            <p><a href="mailto:<?= e(setting('developer_email')) ?>"><?= e(setting('developer_email')) ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Phone</h5>
                            <p><a href="tel:<?= e(setting('developer_phone')) ?>"><?= e(setting('developer_phone')) ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-details">
                            <h5>WhatsApp</h5>
                            <p><a href="https://wa.me/<?= e(str_replace(['+', ' ', '-'], '', setting('developer_whatsapp'))) ?>" target="_blank">Chat on WhatsApp</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Location</h5>
                            <p><?= e(setting('developer_location')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7 fade-in" style="transition-delay: 0.1s;">
                <div class="contact-form">
                    <form id="contactForm" method="POST" action="/contact" novalidate>
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Your Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="your@email.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="phone">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+1 234 567 890">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="subject">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Project Inquiry">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="message">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Tell me about your project..."></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Dynamic hero padding based on navbar height (fixes mobile overlap)
(function() {
    const navbar = document.getElementById('mainNavbar');
    const hero = document.querySelector('.hero-section');
    
    if (!navbar || !hero) return;
    
    function updateHeroPadding() {
        const navbarHeight = navbar.offsetHeight;
        hero.style.paddingTop = navbarHeight + 20 + 'px';
    }
    
    // Initial
    updateHeroPadding();
    
    // On resize
    window.addEventListener('resize', updateHeroPadding);
    
    // On navbar collapse/expand (Bootstrap events)
    navbar.addEventListener('show.bs.collapse', updateHeroPadding);
    navbar.addEventListener('hidden.bs.collapse', updateHeroPadding);
    navbar.addEventListener('shown.bs.collapse', updateHeroPadding);
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
