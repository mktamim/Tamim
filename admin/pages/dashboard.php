<section class="admin-page-heading">
    <div>
        <p class="admin-eyebrow">Control center</p>
        <h1>Dashboard</h1>
        <p class="admin-page-intro">A clear view of your portfolio and the work that needs attention.</p>
    </div>
    <div class="admin-heading-actions">
        <a class="admin-button admin-button-secondary" href="/admin/messages">Review messages <span>→</span></a>
        <a class="admin-button admin-button-primary" href="/admin/projects/new">Add project <span>+</span></a>
    </div>
</section>

<section class="admin-stats-grid" aria-label="Portfolio statistics">
    <a class="admin-stat-card" href="/admin/projects">
        <span class="admin-stat-label">Projects</span>
        <strong><?php echo (int) $counts['projects']; ?></strong>
        <span class="admin-stat-meta">Published work</span>
    </a>
    <a class="admin-stat-card" href="/admin/blog-posts">
        <span class="admin-stat-label">Blog posts</span>
        <strong><?php echo (int) $counts['blog_posts']; ?></strong>
        <span class="admin-stat-meta">Journal entries</span>
    </a>
    <a class="admin-stat-card" href="/admin/messages">
        <span class="admin-stat-label">Messages</span>
        <strong><?php echo (int) $counts['messages']; ?></strong>
        <span class="admin-stat-meta"><?php echo (int) $unreadMessages; ?> unread</span>
    </a>
    <a class="admin-stat-card" href="/admin/services">
        <span class="admin-stat-label">Services</span>
        <strong><?php echo (int) $counts['services']; ?></strong>
        <span class="admin-stat-meta">Active offerings</span>
    </a>
</section>

<section class="admin-dashboard-grid">
    <article class="admin-panel admin-panel-wide">
        <div class="admin-panel-heading">
            <div>
                <p class="admin-eyebrow">Inbox</p>
                <h2>Recent messages</h2>
            </div>
            <a class="admin-text-link" href="/admin/messages">View all <span>→</span></a>
        </div>
        <?php if ($recentMessageRows === []): ?>
            <div class="admin-empty-small">No messages yet.</div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>From</th><th>Subject</th><th>Received</th><th>Status</th><th><span class="sr-only">Action</span></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMessageRows as $message): ?>
                            <tr>
                                <td><strong><?php echo tamim_e($message['name']); ?></strong><small><?php echo tamim_e($message['email']); ?></small></td>
                                <td><?php echo tamim_e($message['subject'] !== '' && $message['subject'] !== null ? $message['subject'] : 'No subject'); ?></td>
                                <td><?php echo tamim_e(tamim_format_date($message['created_at'], 'M d, Y H:i')); ?></td>
                                <td><span class="admin-status admin-status-<?php echo (int) $message['is_read'] ? 'neutral' : 'warning'; ?>"><?php echo (int) $message['is_read'] ? 'Read' : 'New'; ?></span></td>
                                <td><a class="admin-row-action" href="/admin/messages/edit?id=<?php echo (int) $message['id']; ?>">Open</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="admin-panel">
        <div class="admin-panel-heading">
            <div>
                <p class="admin-eyebrow">Latest work</p>
                <h2>Recent projects</h2>
            </div>
            <a class="admin-text-link" href="/admin/projects">View all <span>→</span></a>
        </div>
        <?php if ($recentProjectRows === []): ?>
            <div class="admin-empty-small">No projects yet.</div>
        <?php else: ?>
            <div class="admin-recent-list">
                <?php foreach ($recentProjectRows as $project): ?>
                    <a class="admin-recent-item" href="/admin/projects/edit?id=<?php echo (int) $project['id']; ?>">
                        <span class="admin-recent-icon"><?php echo tamim_e(mb_strtoupper(mb_substr($project['title'], 0, 1))); ?></span>
                        <span><strong><?php echo tamim_e($project['title']); ?></strong><small><?php echo tamim_e(tamim_format_date($project['created_at'], 'M d, Y')); ?></small></span>
                        <?php if ((int) $project['featured'] === 1): ?><span class="admin-status admin-status-success">Featured</span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>

<section class="admin-dashboard-grid admin-dashboard-grid-bottom">
    <article class="admin-panel">
        <div class="admin-panel-heading">
            <div><p class="admin-eyebrow">Content library</p><h2>Quick counts</h2></div>
        </div>
        <div class="admin-count-list">
            <a href="/admin/skills"><span>Skills</span><strong><?php echo (int) $counts['skills']; ?></strong></a>
            <a href="/admin/experience"><span>Experience</span><strong><?php echo (int) $counts['experience']; ?></strong></a>
            <a href="/admin/education"><span>Education</span><strong><?php echo (int) $counts['education']; ?></strong></a>
            <a href="/admin/testimonials"><span>Testimonials</span><strong><?php echo (int) $counts['testimonials']; ?></strong></a>
        </div>
    </article>
    <article class="admin-panel admin-panel-soft">
        <p class="admin-eyebrow">Publishing note</p>
        <h2>Keep the story useful.</h2>
        <p>Review new inquiries, refresh project details and publish journal notes when they offer something concrete to your visitors.</p>
        <a class="admin-button admin-button-secondary" href="/admin/settings">Open site settings <span>→</span></a>
    </article>
</section>
