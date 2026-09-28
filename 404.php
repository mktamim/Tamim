<?php
/**
 * 404 Page
 * Portfolio Website
 */

require_once __DIR__ . '/includes/bootstrap.php';

http_response_code(404);

$pageTitle = '404 - Page Not Found | ' . setting('site_name');
$pageDescription = 'The page you are looking for does not exist.';

require __DIR__ . '/includes/header.php';
?>

<section class="section" style="min-height: 70vh; display: flex; align-items: center; text-align: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="mb-4" style="font-size: 120px; line-height: 1; color: var(--primary-color); font-weight: 800;">404</div>
                <h1 class="display-4 fw-bold mb-3">Page Not Found</h1>
                <p class="lead text-muted mb-5">Sorry, the page you're looking for doesn't exist or has been moved.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="/" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Go Home
                    </a>
                    <a href="/#contact" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contact Me
                    </a>
                </div>
                
                <div class="mt-5">
                    <p class="text-muted">Or explore these sections:</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                        <a href="/#about" class="btn btn-sm btn-outline-secondary">About</a>
                        <a href="/#skills" class="btn btn-sm btn-outline-secondary">Skills</a>
                        <a href="/#projects" class="btn btn-sm btn-outline-secondary">Projects</a>
                        <a href="/#services" class="btn btn-sm btn-outline-secondary">Services</a>
                        <a href="/#contact" class="btn btn-sm btn-outline-secondary">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>