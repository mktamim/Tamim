<?php
require_once 'includes/bootstrap.php';
$where = 'WHERE bp.status = "published"';
$total = db_one('SELECT COUNT(*) as c FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id ' . $where)['c'] ?? 0;
echo 'Published posts: ' . $total . PHP_EOL;
$posts = db_all('SELECT bp.*, bc.name as category_name, bc.slug as category_slug, a.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN admins a ON bp.author_id = a.id ' . $where . ' ORDER BY bp.published_at DESC');
foreach($posts as $p) {
    echo ' - ' . $p['title'] . ' (status: ' . $p['status'] . ', published_at: ' . ($p['published_at'] ?? 'NULL') . ')' . PHP_EOL;
}