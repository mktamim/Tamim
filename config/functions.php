<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function tamim_e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function tamim_redirect(string $path): void
{
    header('Location: ' . $path, true, 302);
    exit;
}

function tamim_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function tamim_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . tamim_e(tamim_csrf_token()) . '">';
}

function tamim_verify_csrf(): bool
{
    $submitted = $_POST['_csrf'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';

    return $submitted !== '' && $expected !== '' && hash_equals($expected, $submitted);
}

function tamim_flash(string $type, string $message): void
{
    $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
}

function tamim_flashes(): array
{
    $flashes = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);

    return $flashes;
}

function tamim_current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    $statement = tamim_pdo()->prepare('SELECT id, name, email, last_login_at FROM admins WHERE id = ?');
    $statement->execute([$_SESSION['admin_id']]);
    $admin = $statement->fetch();

    return $admin ?: null;
}

function tamim_require_admin(): void
{
    if (tamim_current_admin() === null) {
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        $adminBase = $scriptDir ?: '/admin';
        tamim_redirect($adminBase . '/login');
    }
}

function tamim_setting(string $key, ?string $default = null): ?string
{
    static $settings = null;

    if ($settings === null) {
        $statement = tamim_pdo()->query('SELECT setting_key, setting_value FROM settings');
        $settings = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    return array_key_exists($key, $settings) ? (string) $settings[$key] : $default;
}

function tamim_slugify(string $value): string
{
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = preg_replace('/[^a-zA-Z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return strtolower($value) ?: 'item';
}

function tamim_format_date(?string $value, string $format = 'M d, Y'): string
{
    if ($value === null || $value === '') {
        return '';
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? '' : date($format, $timestamp);
}

function tamim_handle_upload(array $file, string $targetDirectory, string $prefix): ?string
{
    if (!isset($file['error']) || !is_int($file['error'])) {
        throw new RuntimeException('Invalid upload data.');
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload failed.');
    }

    if (($file['size'] ?? 0) > 2097152) {
        throw new RuntimeException('Image must be smaller than 2 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mimeType]) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Only JPG, PNG and WEBP images are allowed.');
    }

    $dimensions = getimagesize($file['tmp_name']);
    if ($dimensions === false || ($dimensions[0] ?? 0) > 4000 || ($dimensions[1] ?? 0) > 4000) {
        throw new RuntimeException('Image dimensions are invalid.');
    }

    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }

    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $safeName = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $originalName) ?? '';
    $safeName = trim($safeName, '-') ?: 'image';
    $filename = $prefix . '-' . bin2hex(random_bytes(8)) . '-' . $safeName . '.' . $extensions[$mimeType];
    $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Unable to save uploaded image.');
    }

    return 'assets/uploads/' . $filename;
}

function tamim_delete_upload(?string $path): void
{
    if ($path === null || $path === '') {
        return;
    }

    $root = dirname(__DIR__);
    $fullPath = realpath($root . '/' . ltrim($path, '/'));
    $uploadRoot = realpath($root . '/assets/uploads');

    if ($fullPath !== false && $uploadRoot !== false && str_starts_with($fullPath, $uploadRoot) && is_file($fullPath)) {
        unlink($fullPath);
    }
}

function tamim_login_is_blocked(): bool
{
    $attempts = (int) ($_SESSION['login_attempts'] ?? 0);
    $lockedUntil = (int) ($_SESSION['login_locked_until'] ?? 0);

    return $lockedUntil > time() || $attempts >= 5;
}

function tamim_login_failure(): void
{
    $attempts = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['login_attempts'] = $attempts;

    if ($attempts >= 5) {
        $_SESSION['login_locked_until'] = time() + 300;
    }
}

function tamim_login_success(): void
{
    unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
}
