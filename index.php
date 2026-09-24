<?php
declare(strict_types=1);

require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/includes/template-helpers.php';

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$route = trim($requestPath, '/');
if ($route === '') {
    $route = 'home';
}

$pageTitle = '';
$pageDescription = '';
$bodyClass = 'site-page';
$pageFile = null;

if ($route === 'home' || $route === '') {
    $pageFile = __DIR__ . '/pages/home.php';
    $bodyClass = 'home-page';
} elseif ($route === 'about') {
    $pageFile = __DIR__ . '/pages/about.php';
    $pageTitle = 'About';
    $pageDescription = 'Learn more about Tamim, a web developer building thoughtful digital products.';
    $bodyClass = 'inner-page about-page';
} elseif ($route === 'skills') {
    $pageFile = __DIR__ . '/pages/skills.php';
    $pageTitle = 'Skills';
    $pageDescription = 'Explore the tools and capabilities Tamim uses to build modern websites.';
    $bodyClass = 'inner-page skills-page';
} elseif ($route === 'services') {
    $pageFile = __DIR__ . '/pages/services.php';
    $pageTitle = 'Services';
    $pageDescription = 'Web development, frontend, CMS and performance services from Tamim.';
    $bodyClass = 'inner-page services-page';
} elseif ($route === 'projects') {
    $pageFile = __DIR__ . '/pages/projects.php';
    $pageTitle = 'Projects';
    $pageDescription = 'Selected web projects and product work by Tamim.';
    $bodyClass = 'inner-page projects-page';
} elseif (str_starts_with($route, 'project/')) {
    $pageFile = __DIR__ . '/pages/project.php';
    $pageTitle = 'Project';
    $pageDescription = 'Read more about this selected project.';
    $bodyClass = 'inner-page project-page';
} elseif ($route === 'experience') {
    $pageFile = __DIR__ . '/pages/experience.php';
    $pageTitle = 'Experience';
    $pageDescription = 'Professional experience and career journey of Tamim.';
    $bodyClass = 'inner-page experience-page';
} elseif ($route === 'education') {
    $pageFile = __DIR__ . '/pages/education.php';
    $pageTitle = 'Education';
    $pageDescription = 'Education and continuous learning background.';
    $bodyClass = 'inner-page education-page';
} elseif ($route === 'testimonials') {
    $pageFile = __DIR__ . '/pages/testimonials.php';
    $pageTitle = 'Testimonials';
    $pageDescription = 'Kind words from clients and collaborators.';
    $bodyClass = 'inner-page testimonials-page';
} elseif ($route === 'blog') {
    $pageFile = __DIR__ . '/pages/blog.php';
    $pageTitle = 'Journal';
    $pageDescription = 'Notes on web development, design and building useful products.';
    $bodyClass = 'inner-page blog-page';
} elseif (str_starts_with($route, 'blog/')) {
    $pageFile = __DIR__ . '/pages/blog-post.php';
    $pageTitle = 'Article';
    $pageDescription = 'Read the latest thoughts from Tamim.';
    $bodyClass = 'inner-page blog-post-page';
} elseif ($route === 'contact') {
    $pageFile = __DIR__ . '/pages/contact.php';
    $pageTitle = 'Contact';
    $pageDescription = 'Get in touch with Tamim about your next web project.';
    $bodyClass = 'inner-page contact-page';
} else {
    $pageFile = __DIR__ . '/pages/404.php';
    $pageTitle = 'Page not found';
    $pageDescription = 'The requested page could not be found.';
    $bodyClass = 'inner-page error-page';
    http_response_code(404);
}

try {
    $pdo = tamim_pdo();
} catch (Throwable $error) {
    http_response_code(503);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Service unavailable</title><link rel="stylesheet" href="/assets/css/style.css"></head><body class="install-body"><main class="install-shell"><section class="install-card"><p class="eyebrow">Tamim Portfolio</p><h1>Database connection unavailable</h1><p class="muted">Please run the installer or check your database configuration.</p><a class="button button-primary" href="/database/install.php">Open installer</a></section></main></body></html>';
    exit;
}

ob_start();
require __DIR__ . '/includes/header.php';
require $pageFile;
require __DIR__ . '/includes/footer.php';
echo ob_get_clean();
