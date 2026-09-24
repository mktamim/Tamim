<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/init.php';
require_once __DIR__ . '/includes/admin-functions.php';

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/', PHP_URL_PATH) ?: '/admin/';
if (!str_starts_with($requestPath, '/admin')) {
    tamim_redirect('/admin/');
}

$route = trim(substr($requestPath, strlen('/admin')), '/');
if ($route === 'assets/admin.css') {
    $asset = __DIR__ . '/assets/admin.css';
    if (is_file($asset)) {
        header('Content-Type: text/css; charset=UTF-8');
        header('Cache-Control: public, max-age=3600');
        readfile($asset);
    } else {
        http_response_code(404);
    }
    exit;
}

$segments = $route === '' ? [] : array_values(array_filter(explode('/', $route), static fn (string $value): bool => $value !== ''));
$resourceName = $segments[0] ?? '';
$actionName = $segments[1] ?? '';
$requestedId = (int) ($_POST['id'] ?? $_GET['id'] ?? ($segments[2] ?? 0));
$requestedId = $requestedId > 0 ? $requestedId : 0;

function admin_login_is_blocked(): bool
{
    $lockedUntil = (int) ($_SESSION['admin_login_locked_until'] ?? 0);
    if ($lockedUntil > time()) {
        return true;
    }

    if ($lockedUntil > 0) {
        unset($_SESSION['admin_login_attempts'], $_SESSION['admin_login_locked_until']);
    }

    return (int) ($_SESSION['admin_login_attempts'] ?? 0) >= 5;
}

function admin_login_failure(): void
{
    $attempts = (int) ($_SESSION['admin_login_attempts'] ?? 0) + 1;
    $_SESSION['admin_login_attempts'] = $attempts;
    if ($attempts >= 5) {
        $_SESSION['admin_login_locked_until'] = time() + 300;
    }
}

function admin_login_success(): void
{
    unset($_SESSION['admin_login_attempts'], $_SESSION['admin_login_locked_until']);
}

if ($resourceName === 'login') {
    if (tamim_current_admin() !== null) {
        tamim_redirect('/admin/');
    }

    $loginMessage = '';
    $loginMessageType = 'error';
    $loginEmail = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!tamim_verify_csrf()) {
            $loginMessage = 'The security token expired. Please try again.';
        } elseif (admin_login_is_blocked()) {
            $loginMessage = 'Too many failed sign-in attempts. Please wait five minutes and try again.';
        } else {
            $loginEmail = admin_post_string('email');
            $password = (string) ($_POST['password'] ?? '');
            $email = filter_var($loginEmail, FILTER_VALIDATE_EMAIL);

            if ($email === false || $password === '') {
                admin_login_failure();
                $loginMessage = 'Email and password are required.';
            } else {
                try {
                    $statement = tamim_pdo()->prepare('SELECT id, password_hash FROM admins WHERE email = ?');
                    $statement->execute([$email]);
                    $account = $statement->fetch();
                    $passwordHash = is_array($account) ? (string) $account['password_hash'] : password_hash('invalid-account', PASSWORD_DEFAULT);

                    if (is_array($account) && password_verify($password, $passwordHash)) {
                        session_regenerate_id(true);
                        $_SESSION['admin_id'] = (int) $account['id'];
                        $update = tamim_pdo()->prepare('UPDATE admins SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?');
                        $update->execute([(int) $account['id']]);
                        if (password_needs_rehash($passwordHash, PASSWORD_DEFAULT)) {
                            $rehash = tamim_pdo()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
                            $rehash->execute([password_hash($password, PASSWORD_DEFAULT), (int) $account['id']]);
                        }
                        admin_login_success();
                        tamim_redirect('/admin/');
                    }

                    admin_login_failure();
                    $loginMessage = 'The email or password is incorrect.';
                } catch (Throwable) {
                    $loginMessage = 'Sign in is temporarily unavailable.';
                }
            }
        }
    }

    $pageTitle = 'Sign in';
    require __DIR__ . '/pages/login.php';
    exit;
}

if ($resourceName === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !tamim_verify_csrf() || tamim_current_admin() === null) {
        tamim_redirect('/admin/login');
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    tamim_redirect('/admin/login');
}

tamim_require_admin();
$admin = tamim_current_admin();
$unreadMessages = (int) tamim_pdo()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$currentRoute = $resourceName === '' || $resourceName === 'dashboard' ? 'dashboard' : $resourceName;

if ($resourceName === '' || $resourceName === 'dashboard') {
    $counts = [];
    foreach (['skills', 'services', 'projects', 'experience', 'education', 'testimonials', 'messages', 'blog_posts'] as $table) {
        $counts[$table] = (int) tamim_pdo()->query('SELECT COUNT(*) FROM ' . admin_identifier($table))->fetchColumn();
    }
    $recentMessages = tamim_pdo()->prepare('SELECT id, name, email, subject, message, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 6');
    $recentMessages->execute();
    $recentMessageRows = $recentMessages->fetchAll();
    $recentProjects = tamim_pdo()->prepare('SELECT id, title, slug, featured, created_at FROM projects ORDER BY created_at DESC LIMIT 5');
    $recentProjects->execute();
    $recentProjectRows = $recentProjects->fetchAll();
    $pageTitle = 'Dashboard';
    require __DIR__ . '/includes/header.php';
    require __DIR__ . '/pages/dashboard.php';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($resourceName === 'settings') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        admin_verify_post();
        $settingsPdo = tamim_pdo();
        $action = admin_post_string('action');
        $key = admin_post_string('setting_key');

        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]{1,79}$/', $key)) {
            tamim_flash('error', 'Setting keys must use letters, numbers and underscores.');
        } elseif ($action === 'create') {
            $value = trim((string) ($_POST['setting_value'] ?? ''));
            if (mb_strlen($value) > 100000) {
                tamim_flash('error', 'Setting value is too long.');
            } else {
                try {
                    $insert = $settingsPdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)');
                    $insert->execute([':key' => $key, ':value' => $value]);
                    tamim_flash('success', 'Setting created.');
                } catch (Throwable) {
                    tamim_flash('error', 'That setting key already exists.');
                }
            }
        } elseif ($action === 'update') {
            $value = trim((string) ($_POST['setting_value'] ?? ''));
            $check = $settingsPdo->prepare('SELECT setting_key FROM settings WHERE setting_key = ?');
            $check->execute([$key]);
            if ($check->fetchColumn() === false) {
                tamim_flash('error', 'Setting was not found.');
            } elseif (mb_strlen($value) > 100000) {
                tamim_flash('error', 'Setting value is too long.');
            } else {
                $update = $settingsPdo->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?');
                $update->execute([$value, $key]);
                tamim_flash('success', 'Setting updated.');
            }
        } elseif ($action === 'delete') {
            $check = $settingsPdo->prepare('SELECT setting_key FROM settings WHERE setting_key = ?');
            $check->execute([$key]);
            if ($check->fetchColumn() === false) {
                tamim_flash('error', 'Setting was not found.');
            } else {
                $delete = $settingsPdo->prepare('DELETE FROM settings WHERE setting_key = ?');
                $delete->execute([$key]);
                tamim_flash('success', 'Setting deleted.');
            }
        } else {
            tamim_flash('error', 'Unknown setting action.');
        }

        tamim_redirect('/admin/settings');
    }

    $settings = tamim_pdo()->query('SELECT setting_key, setting_value, updated_at FROM settings ORDER BY setting_key')->fetchAll();
    $pageTitle = 'Settings';
    require __DIR__ . '/includes/header.php';
    require __DIR__ . '/pages/settings.php';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$resource = admin_resource_config($resourceName);
if ($resource === null) {
    http_response_code(404);
    $pageTitle = 'Page not found';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="admin-empty">
        <p class="admin-eyebrow">404</p>
        <h1>That admin page does not exist.</h1>
        <a class="admin-button admin-button-primary" href="/admin/">Return to dashboard</a>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $actionName === 'delete') {
    admin_verify_post();
    if ($requestedId === 0) {
        tamim_flash('error', $resource['singular'] . ' was not found.');
    } else {
        $deleted = admin_delete_resource(tamim_pdo(), $resource, $requestedId);
        if ($deleted === null) {
            tamim_flash('error', $resource['singular'] . ' was not found.');
        } else {
            foreach ($resource['fields'] as $field) {
                if ($field['type'] === 'image' && !empty($deleted[$field['name']])) {
                    tamim_delete_upload((string) $deleted[$field['name']]);
                }
            }
            tamim_flash('success', $resource['singular'] . ' deleted.');
        }
    }
    tamim_redirect(admin_url($resourceName));
}

if ($actionName === 'new' || ($actionName === 'edit' && $requestedId > 0)) {
    $editing = $actionName === 'edit';
    $current = $editing ? admin_fetch_resource(tamim_pdo(), $resource, $requestedId) : null;
    if ($editing && $current === null) {
        tamim_flash('error', $resource['singular'] . ' was not found.');
        tamim_redirect(admin_url($resourceName));
    }

    $formValues = [];
    $formErrors = [];
    $deleteAfterFailure = [];
    $deleteAfterSuccess = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        admin_verify_post();
        [$validatedValues, $formErrors] = admin_validate_fields(tamim_pdo(), $resource, $current, $editing);
        [$uploadedValues, $deleteAfterSuccess, $deleteAfterFailure, $uploadErrors] = admin_process_uploads($resource, $current);
        $formValues = array_merge($validatedValues, $uploadedValues);
        $formErrors = array_merge($formErrors, $uploadErrors);

        if ($formErrors === []) {
            if (admin_store_resource(tamim_pdo(), $resource, $formValues, $editing ? $requestedId : null)) {
                tamim_flash('success', $resource['singular'] . ($editing ? ' updated.' : ' created.'));
                tamim_redirect(admin_url($resourceName));
            }

            admin_delete_files($deleteAfterFailure);
            $formErrors['form'] = 'The record could not be saved. Please review the details and try again.';
        } else {
            admin_delete_files($deleteAfterFailure);
        }
    } else {
        foreach ($resource['fields'] as $field) {
            $formValues[$field['name']] = $current[$field['name']] ?? '';
        }
    }

    $hasImageFields = array_some($resource['fields'], static fn (array $field): bool => $field['type'] === 'image');
    $pageTitle = ($editing ? 'Edit ' : 'New ') . $resource['singular'];
    require __DIR__ . '/includes/header.php';
    require __DIR__ . '/pages/resource-form.php';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page = max(1, (int) ($_GET['page'] ?? 1));
[$records, $recordCount, $pageCount] = admin_list_resource(tamim_pdo(), $resource, $page);
$pageTitle = $resource['plural'];
require __DIR__ . '/includes/header.php';
require __DIR__ . '/pages/resource-list.php';
require __DIR__ . '/includes/footer.php';
