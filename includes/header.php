<?php
$siteName = tamim_setting('site_name', 'Tamim');
$siteTitle = $pageTitle !== '' ? $pageTitle . ' | ' . $siteName : (tamim_setting('site_title', $siteName . ' | Web Developer') ?: $siteName);
$siteDescription = $pageDescription !== '' ? $pageDescription : tamim_setting('site_description', '');
$currentPage = $route;
$navItems = [
    ['home', 'Home', '/'],
    ['about', 'About', '/about'],
    ['services', 'Services', '/services'],
    ['projects', 'Projects', '/projects'],
    ['experience', 'Experience', '/experience'],
    ['blog', 'Journal', '/blog'],
    ['contact', 'Contact', '/contact'],
];
$canonicalUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$ogImage = tamim_setting('og_image', '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo tamim_e($siteTitle); ?></title>
    <meta name="description" content="<?php echo tamim_e($siteDescription); ?>">
    <meta property="og:title" content="<?php echo tamim_e($siteTitle); ?>">
    <meta property="og:description" content="<?php echo tamim_e($siteDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo tamim_e($siteName); ?>">
    <meta property="og:url" content="<?php echo tamim_e($canonicalUrl); ?>">
    <?php if ($ogImage !== ''): ?>
    <meta property="og:image" content="<?php echo tamim_e(tamim_url($ogImage)); ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php echo tamim_e($canonicalUrl); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/main.js" defer></script>
</head>
<body class="<?php echo tamim_e($bodyClass); ?>">
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="/" aria-label="<?php echo tamim_e($siteName); ?> home"><span class="brand-mark">T</span><span><?php echo tamim_e($siteName); ?></span></a>
        <button class="nav-toggle" type="button" aria-controls="primary-navigation" aria-expanded="false"><span></span><span></span><span></span><span class="sr-only">Toggle navigation</span></button>
        <nav id="primary-navigation" class="primary-nav" aria-label="Primary navigation">
            <?php foreach ($navItems as $item): ?>
                <a class="<?php echo $currentPage === $item[0] ? 'active' : ''; ?>" href="<?php echo tamim_e($item[2]); ?>"><?php echo tamim_e($item[1]); ?></a>
            <?php endforeach; ?>
            <a class="button button-small nav-cta" href="/contact">Let us talk</a>
        </nav>
    </div>
</header>
<main id="main-content">
