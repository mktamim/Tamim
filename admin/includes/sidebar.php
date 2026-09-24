<aside id="admin-navigation" class="admin-sidebar">
    <div class="admin-brand">
        <a href="/admin/"><span class="admin-brand-mark">T</span><span>Tamim <small>Admin</small></span></a>
    </div>
    <nav class="admin-nav" aria-label="Admin navigation">
        <a class="<?php echo ($currentRoute === 'dashboard') ? 'active' : ''; ?>" href="/admin/"><span class="nav-icon">D</span>Dashboard</a>
        <a class="<?php echo ($currentRoute === 'settings') ? 'active' : ''; ?>" href="/admin/settings"><span class="nav-icon">S</span>Settings</a>
        <p class="admin-nav-label">Content</p>
        <a class="<?php echo ($currentRoute === 'skills') ? 'active' : ''; ?>" href="/admin/skills"><span class="nav-icon">K</span>Skills</a>
        <a class="<?php echo ($currentRoute === 'services') ? 'active' : ''; ?>" href="/admin/services"><span class="nav-icon">V</span>Services</a>
        <a class="<?php echo ($currentRoute === 'projects') ? 'active' : ''; ?>" href="/admin/projects"><span class="nav-icon">P</span>Projects</a>
        <a class="<?php echo ($currentRoute === 'experience') ? 'active' : ''; ?>" href="/admin/experience"><span class="nav-icon">E</span>Experience</a>
        <a class="<?php echo ($currentRoute === 'education') ? 'active' : ''; ?>" href="/admin/education"><span class="nav-icon">U</span>Education</a>
        <a class="<?php echo ($currentRoute === 'testimonials') ? 'active' : ''; ?>" href="/admin/testimonials"><span class="nav-icon">Q</span>Testimonials</a>
        <a class="<?php echo ($currentRoute === 'messages') ? 'active' : ''; ?>" href="/admin/messages"><span class="nav-icon">M</span>Messages<?php if (($unreadMessages ?? 0) > 0): ?><span class="nav-count"><?php echo (int) $unreadMessages; ?></span><?php endif; ?></a>
        <a class="<?php echo ($currentRoute === 'blog-posts') ? 'active' : ''; ?>" href="/admin/blog-posts"><span class="nav-icon">B</span>Blog posts</a>
    </nav>
    <div class="admin-sidebar-foot">
        <span>Secure portfolio control</span>
        <form method="post" action="/admin/logout">
            <?php echo tamim_csrf_field(); ?>
            <button class="admin-logout" type="submit">Sign out</button>
        </form>
    </div>
</aside>
