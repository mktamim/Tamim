<?php
/**
 * Frontend Footer
 * Portfolio Website - Includes
*/
?>
    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="container">
            <div class="row g-5">
                <!-- About -->
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <?php if (setting('logo')): ?>
                            <img src="<?= e(setting('site_url', '/Tamim') . '/assets/uploads/settings/' . setting('logo')) ?>" alt="<?= e(setting('site_name')) ?>" style="height: 40px;">
                        <?php else: ?>
                            <span class="fw-bold fs-3" style="color: var(--primary-color);"><?= e(setting('developer_name', 'Developer')) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-white"><?= e(setting('about_short_intro', 'Professional web developer creating modern, fast, and scalable web applications.')) ?></p>
                    
                    <!-- Social Links -->
                    <div class="social-links mt-4">
                        <?php foreach (social_links() as $social): ?>
                            <a href="<?= e($social['url']) ?>" class="social-link" target="_blank" rel="noopener" title="<?= e($social['label']) ?>">
                                <i class="<?= e($social['icon_class']) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="/Tamim/#home">Home</a></li>
                        <li><a href="/Tamim/#about">About</a></li>
                        <li><a href="/Tamim/#skills">Skills</a></li>
                        <li><a href="/Tamim/#services">Services</a></li>
                        <li><a href="/Tamim/#projects">Projects</a></li>
                        <li><a href="/Tamim/#experience">Experience</a></li>
                        <li><a href="/Tamim/#contact">Contact</a></li>
                    </ul>
                </div>
                
                <!-- Services -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Services</h5>
                    <ul class="footer-links">
                        <?php foreach (services(true) as $service): ?>
                            <li><a href="/Tamim/#services"><?= e($service['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">Contact Info</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:<?= e(setting('developer_email')) ?>"><?= e(setting('developer_email')) ?></a>
                        </li>
                        <li>
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:<?= e(setting('developer_phone')) ?>"><?= e(setting('developer_phone')) ?></a>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp me-2"></i>
                            <a href="https://wa.me/<?= e(str_replace(['+', ' ', '-'], '', setting('developer_whatsapp'))) ?>" target="_blank">WhatsApp</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span><?= e(setting('developer_location')) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="row mt-5">
                <div class="col-12">
                    <hr class="footer-divider">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <p class="mb-0 text-white small">
                            &copy; <?= date('Y') ?> <?= e(setting('developer_name', 'Developer')) ?>. All Rights Reserved.
                        </p>
                        <p class="mb-0 text-white small">
                            Design & Development by Tamim iqbal
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top -->
    <button class="btn btn-primary back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Live Chat Widget -->
    <?php include __DIR__ . '/live-chat-widget.php'; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>