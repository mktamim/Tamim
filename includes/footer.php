</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand brand-light" href="/"><span class="brand-mark">T</span><span><?php echo tamim_e(tamim_setting('site_name', 'Tamim')); ?></span></a>
            <p class="footer-note">Thoughtful web development for people with something meaningful to share.</p>
        </div>
        <div class="footer-links">
            <a href="/about">About</a>
            <a href="/projects">Work</a>
            <a href="/blog">Journal</a>
            <a href="/contact">Contact</a>
        </div>
        <div class="social-links" aria-label="Social links">
            <a href="<?php echo tamim_e(tamim_setting('social_github', '#')); ?>" aria-label="GitHub">GH</a>
            <a href="<?php echo tamim_e(tamim_setting('social_linkedin', '#')); ?>" aria-label="LinkedIn">IN</a>
            <a href="<?php echo tamim_e(tamim_setting('social_twitter', '#')); ?>" aria-label="Twitter">X</a>
        </div>
    </div>
    <div class="container footer-bottom"><span>© <?php echo date('Y'); ?> <?php echo tamim_e(tamim_setting('copyright', 'Tamim')); ?></span><a href="/admin/login">Admin</a></div>
</footer>
</body>
</html>
