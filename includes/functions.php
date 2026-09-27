<?php
/**
 * Core Functions
 * Portfolio Website - Includes
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    $sessionConfig = config('session');
    session_set_cookie_params([
        'lifetime' => $sessionConfig['lifetime'],
        'path' => '/',
        'domain' => '',
        'secure' => $sessionConfig['secure'],
        'httponly' => $sessionConfig['httponly'],
        'samesite' => $sessionConfig['samesite'],
    ]);
    session_name($sessionConfig['name']);
    session_start();
}

// Load configuration
function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config/app.php';
    }
    $keys = explode('.', $key);
    $value = $config;
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }
    return $value;
}

// Get database connection
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dbConfig = config('database');
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    }
    return $pdo;
}

// Execute query and return all results
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Execute query and return single result
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() ?: null;
}

// Execute query and return affected rows
function db_execute(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

// Get last insert ID
function db_last_id(): string
{
    return db()->lastInsertId();
}

// Sanitize output for HTML
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Sanitize output for HTML attributes
function ea($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function csrf_verify(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Generate CSRF field for forms
function csrf_field(): string
{
    return '<input type="hidden" name="' . config('security.csrf_token_name') . '" value="' . e(csrf_token()) . '">';
}

// Check if user is logged in (admin)
function auth_check(): bool
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Get current admin user
function auth_user(): ?array
{
    if (!auth_check()) {
        return null;
    }
    return db_one('SELECT * FROM admins WHERE id = ?', [$_SESSION['admin_id']]);
}

// Login admin
function auth_login(int $adminId, bool $remember = false): void
{
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_login_time'] = time();
    if ($remember) {
        // Could implement remember me token here
    }
    db_execute('UPDATE admins SET last_login = NOW(), login_attempts = 0, locked_until = NULL WHERE id = ?', [$adminId]);
}

// Logout admin
function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

// Redirect with message
function redirect(string $url, string $message = '', string $type = 'success'): never
{
    if ($message) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    session_write_close();
    header("Location: $url");
    exit;
}

// Get flash message
function flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Get setting value
function setting(string $key, $default = null)
{
    $row = db_one('SELECT setting_value, setting_type FROM settings WHERE setting_key = ? AND is_public = 1', [$key]);
    if (!$row) {
        return $default;
    }
    $value = $row['setting_value'];
    switch ($row['setting_type']) {
        case 'json':
            return json_decode($value, true) ?? $default;
        case 'image':
            return $value ?: $default;
        default:
            return $value ?: $default;
    }
}

// Get all settings by group
function settings_group(string $group): array
{
    $rows = db_all('SELECT setting_key, setting_value, setting_type FROM settings WHERE group_name = ? AND is_public = 1 ORDER BY sort_order', [$group]);
    $result = [];
    foreach ($rows as $row) {
        $value = $row['setting_value'];
        if ($row['setting_type'] === 'json') {
            $value = json_decode($value, true) ?? [];
        }
        $result[$row['setting_key']] = $value;
    }
    return result;
}

// Get active social links
function social_links(): array
{
    return db_all('SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order');
}

// Get active skills
function skills(string $category = null): array
{
    $sql = 'SELECT * FROM skills WHERE is_active = 1';
    $params = [];
    if ($category) {
        $sql .= ' AND category = ?';
        $params[] = $category;
    }
    $sql .= ' ORDER BY sort_order';
    return db_all($sql, $params);
}

// Get active services
function services(bool $featuredOnly = false): array
{
    $sql = 'SELECT * FROM services WHERE is_active = 1';
    if ($featuredOnly) {
        $sql .= ' AND is_featured = 1';
    }
    $sql .= ' ORDER BY sort_order';
    return db_all($sql);
}

// Get active project categories
function project_categories(): array
{
    return db_all('SELECT * FROM project_categories WHERE is_active = 1 ORDER BY sort_order');
}

// Get projects with optional filters
function projects(array $filters = []): array
{
    $sql = 'SELECT p.*, pc.name as category_name, pc.slug as category_slug FROM projects p LEFT JOIN project_categories pc ON p.category_id = pc.id WHERE p.is_active = 1';
    $params = [];
    if (!empty($filters['category'])) {
        $sql .= ' AND pc.slug = ?';
        $params[] = $filters['category'];
    }
    if (!empty($filters['featured'])) {
        $sql .= ' AND p.is_featured = 1';
    }
    $sql .= ' ORDER BY p.sort_order, p.created_at DESC';
    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . (int)$filters['limit'];
    }
    return db_all($sql, $params);
}

// Get single project by slug
function project_by_slug(string $slug): ?array
{
    return db_one('SELECT p.*, pc.name as category_name, pc.slug as category_slug FROM projects p LEFT JOIN project_categories pc ON p.category_id = pc.id WHERE p.slug = ? AND p.is_active = 1', [$slug]);
}

// Get active experiences
function experiences(): array
{
    return db_all('SELECT * FROM experiences WHERE is_active = 1 ORDER BY start_date DESC');
}

// Get active educations
function educations(): array
{
    return db_all('SELECT * FROM educations WHERE is_active = 1 ORDER BY start_year DESC');
}

// Get active testimonials
function testimonials(bool $featuredOnly = false): array
{
    $sql = 'SELECT * FROM testimonials WHERE is_active = 1';
    if ($featuredOnly) {
        $sql .= ' AND is_featured = 1';
    }
    $sql .= ' ORDER BY sort_order';
    return db_all($sql);
}

// Get published blog posts
function blog_posts(array $filters = []): array
{
    $sql = 'SELECT bp.*, bc.name as category_name, bc.slug as category_slug, a.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN admins a ON bp.author_id = a.id WHERE bp.status = "published"';
    $params = [];
    if (!empty($filters['category'])) {
        $sql .= ' AND bc.slug = ?';
        $params[] = $filters['category'];
    }
    if (!empty($filters['featured'])) {
        $sql .= ' AND bp.is_featured = 1';
    }
    $sql .= ' ORDER BY bp.published_at DESC';
    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . (int)$filters['limit'];
    }
    return db_all($sql, $params);
}

// Get blog post by slug
function blog_post_by_slug(string $slug): ?array
{
    return db_one('SELECT bp.*, bc.name as category_name, bc.slug as category_slug, a.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN admins a ON bp.author_id = a.id WHERE bp.slug = ? AND bp.status = "published"', [$slug]);
}

// Get blog categories
function blog_categories(): array
{
    return db_all('SELECT * FROM blog_categories WHERE is_active = 1 ORDER BY sort_order');
}

// Generate slug from string
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

// Generate unique slug
function unique_slug(string $table, string $baseSlug, string $column = 'slug', $excludeId = null): string
{
    $slug = $baseSlug;
    $counter = 1;
    while (true) {
        $sql = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";
        $params = [$slug];
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $count = db_one($sql, $params)['COUNT(*)'] ?? 0;
        if ($count === 0) {
            break;
        }
        $slug = $baseSlug . '-' . $counter++;
    }
    return $slug;
}

// Upload image
function upload_image(array $file, string $subdir = ''): array
{
    $config = config('upload');
    $errors = [];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload failed with error code: ' . $file['error'];
        return ['success' => false, 'errors' => $errors];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $config['allowed_types'])) {
        $errors[] = 'Invalid file type. Allowed: JPG, PNG, WebP';
        return ['success' => false, 'errors' => $errors];
    }
    if ($file['size'] > $config['max_size']) {
        $errors[] = 'File size exceeds limit of 5MB';
        return ['success' => false, 'errors' => $errors];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $config['allowed_extensions'])) {
        $errors[] = 'Invalid file extension';
        return ['success' => false, 'errors' => $errors];
    }
    list($width, $height) = getimagesize($file['tmp_name']);
    if ($width > $config['image_max_width'] || $height > $config['image_max_height']) {
        $errors[] = "Image dimensions too large. Max: {$config['image_max_width']}x{$config['image_max_height']}";
        return ['success' => false, 'errors' => $errors];
    }
    $uploadDir = $config['path'] . '/' . $subdir;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        $errors[] = 'Failed to move uploaded file';
        return ['success' => false, 'errors' => $errors];
    }
    // Convert to WebP if not already
    if ($ext !== 'webp') {
        $webpPath = $uploadDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        if ($mime === 'image/jpeg') {
            $image = imagecreatefromjpeg($filepath);
        } elseif ($mime === 'image/png') {
            $image = imagecreatefrompng($filepath);
        } else {
            $image = null;
        }
        if ($image) {
            imagewebp($image, $webpPath, 85);
            imagedestroy($image);
            unlink($filepath);
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        }
    }
    return [
        'success' => true,
        'filename' => $filename,
        'path' => $config['url'] . '/' . $subdir . '/' . $filename,
        'full_path' => $config['path'] . '/' . $subdir . '/' . $filename,
    ];
}

// Delete file
function delete_file(string $path): bool
{
    $fullPath = config('upload.path') . '/' . ltrim($path, '/');
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

// Format date
function format_date(string $date, string $format = 'M d, Y'): string
{
    $dt = new DateTime($date);
    return $dt->format($format);
}

// Format relative time
function time_ago(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return format_date($datetime);
}

// Get asset URL with version
function asset(string $path): string
{
    $base = config('app.url');
    $version = config('app.debug') ? time() : '1.0.0';
    return "$base/assets/$path?v=$version";
}

// Get current URL
function current_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Check if current route matches
function is_active(string $route): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = parse_url(config('app.url'), PHP_URL_PATH);
    $uri = str_replace($base, '', $uri);
    return $uri === $route || $uri === $route . '/' ? 'active' : '';
}

// Get client IP
function get_client_ip(): string
{
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            return trim($ips[0]);
        }
    }
    return 'unknown';
}

// JSON response
function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Validate email
function valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate URL
function valid_url(string $url): bool
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Sanitize filename
function sanitize_filename(string $filename): string
{
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    $filename = preg_replace('/_{2,}/', '_', $filename);
    return $filename;
}

// Get pagination links
function paginate(int $total, int $perPage, int $currentPage, string $baseUrl): array
{
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'start' => $start,
        'end' => $end,
        'base_url' => $baseUrl,
    ];
}

// Render pagination HTML
function render_pagination(array $pagination): string
{
    if ($pagination['total_pages'] <= 1) return '';
    $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center">';
    if ($pagination['current_page'] > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $pagination['base_url'] . '?page=' . ($pagination['current_page'] - 1) . '">Previous</a></li>';
    }
    for ($i = $pagination['start']; $i <= $pagination['end']; $i++) {
        $active = $i === $pagination['current_page'] ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $pagination['base_url'] . '?page=' . $i . '">' . $i . '</a></li>';
    }
    if ($pagination['current_page'] < $pagination['total_pages']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $pagination['base_url'] . '?page=' . ($pagination['current_page'] + 1) . '">Next</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}