<?php
/**
 * Frontend Header
 * Portfolio Website - Includes
 */
$pageTitle = $pageTitle ?? setting('site_name', 'My Portfolio');
$pageDescription = $pageDescription ?? setting('seo_description', '');
$pageKeywords = $pageKeywords ?? setting('seo_keywords', '');
$pageUrl = $pageUrl ?? current_url();
$pageImage = $pageImage ?? (setting('profile_image') ? setting('site_url', '/Tamim') . '/assets/uploads/settings/' . setting('profile_image') : '');
$pageType = $pageType ?? 'website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="keywords" content="<?= e($pageKeywords) ?>">
    <meta name="author" content="<?= e(setting('developer_name', 'Developer')) ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= e($pageType) ?>">
    <meta property="og:url" content="<?= e($pageUrl) ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <?php if ($pageImage): ?>
        <meta property="og:image" content="<?= e($pageImage) ?>">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= e($pageUrl) ?>">
    <meta property="twitter:title" content="<?= e($pageTitle) ?>">
    <meta property="twitter:description" content="<?= e($pageDescription) ?>">
    <?php if ($pageImage): ?>
        <meta property="twitter:image" content="<?= e($pageImage) ?>">
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= e($pageUrl) ?>">
    
    <!-- Favicon -->
    <?php if (setting('favicon')): ?>
        <link rel="icon" type="image/x-icon" href="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . setting('favicon')) ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    <?php endif; ?>
    
    <title><?= e($pageTitle) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: <?= e(setting('primary_color', '#2563eb')) ?>;
            --secondary-color: <?= e(setting('secondary_color', '#0ea5e9')) ?>;
            --background-color: <?= e(setting('background_color', '#ffffff')) ?>;
            --hero-background-color: <?= e(setting('hero_background_color', '#DBDBDB')) ?>;
            --text-color: <?= e(setting('text_color', '#1f2937')) ?>;
            --primary-rgb: 37, 99, 235;
            --secondary-rgb: 14, 165, 233;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNavbar">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/">
                <?php if (setting('logo')): ?>
                    <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . setting('logo')) ?>" alt="<?= e(setting('site_name')) ?>" style="height: 40px;">
                <?php else: ?>
                    <span class="fw-bold fs-4" style="color: var(--primary-color);"><?= e(setting('developer_name', 'Developer')) ?></span>
                <?php endif; ?>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Nav Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4">
                    <li class="nav-item">
                        <a class="nav-link" href="/#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#skills">Skills</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#projects">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#experience">Experience</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#contact">Contact</a>
                    </li>
                </ul>
                
                <!-- CTA Button -->
                <a href="/#contact" class="btn btn-primary d-none d-lg-inline-flex">
                    <i class="fas fa-comment me-2"></i>Let's Talk
                </a>
            </div>
        </div>
    </nav>