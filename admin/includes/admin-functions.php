<?php
declare(strict_types=1);

function admin_url(string $path = ''): string
{
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $adminBase = $scriptDir ?: '/admin';
    $path = trim($path, '/');

    return $path === '' ? $adminBase . '/' : $adminBase . '/' . $path;
}

function admin_back_url(string $fallback): string
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $parts = $referer === '' ? [] : parse_url($referer);
    $path = is_array($parts) ? ($parts['path'] ?? '') : '';

    if ($path !== '' && str_starts_with($path, '/admin/') && !str_starts_with($path, '//')) {
        return $path;
    }

    return $fallback;
}

function admin_require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return;
    }

    http_response_code(405);
    header('Allow: POST');
    tamim_flash('error', 'That action requires a POST request.');
    tamim_redirect(admin_back_url(admin_url()));
}

function admin_verify_post(): void
{
    admin_require_post();

    if (!tamim_verify_csrf()) {
        tamim_flash('error', 'The security token expired. Please try again.');
        tamim_redirect(admin_back_url(admin_url()));
    }
}

function admin_post_string(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function admin_sanitize_html(string $value): string
{
    $value = str_replace("\0", '', $value);

    return trim(strip_tags($value, '<p><br><strong><em><a><ul><ol><li><h2><h3><h4>'));
}

function admin_valid_url(?string $value): bool
{
    if ($value === null || $value === '' || $value === '#') {
        return true;
    }

    if (str_starts_with($value, '/') && !str_starts_with($value, '//')) {
        return true;
    }

    return filter_var($value, FILTER_VALIDATE_URL) !== false
        && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
}

function admin_validate_fields(PDO $pdo, array $definition, ?array $current, bool $editing): array
{
    $values = [];
    $errors = [];
    $title = '';

    foreach ($definition['fields'] as $field) {
        $name = $field['name'];
        $type = $field['type'];
        $required = $field['required'] ?? false;
        $raw = admin_post_string($name);

        if ($type === 'image') {
            continue;
        }

        if ($type === 'boolean') {
            $value = isset($_POST[$name]) ? 1 : 0;
        } elseif ($type === 'integer' || $type === 'year') {
            $value = $raw === '' ? null : filter_var($raw, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0, 'max_range' => 9999],
            ]);
        } elseif ($type === 'datetime') {
            if ($raw === '') {
                $value = null;
            } else {
                $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $raw);
                $value = $date ? $date->format('Y-m-d H:i:s') : false;
            }
        } elseif ($type === 'date') {
            if ($raw === '') {
                $value = null;
            } else {
                $date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw);
                $value = $date && $date->format('Y-m-d') === $raw ? $raw : false;
            }
        } elseif ($type === 'json') {
            try {
                $items = str_starts_with($raw, '[')
                    ? json_decode($raw, true, 512, JSON_THROW_ON_ERROR)
                    : (preg_split('/\R+/', $raw) ?: []);
                $items = is_array($items) ? $items : [];
                $items = array_values(array_filter(array_map('trim', $items), static fn (string $item): bool => $item !== ''));
                if ($required && $items === []) {
                    $errors[$name] = sprintf('%s is required.', $field['label']);
                    $values[$name] = null;
                    continue;
                }
                $value = json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                $value = false;
            }
        } elseif ($type === 'richtext') {
            $value = admin_sanitize_html($raw);
        } else {
            $value = $raw;
        }

        if ($type === 'slug') {
            if ($value === '' && $editing && is_array($current) && isset($current[$name])) {
                $value = (string) $current[$name];
            } elseif ($value === '' && $title !== '') {
                $value = tamim_slugify($title);
            }
        }

        if ($type === 'slug' && $title === '') {
            $title = (string) ($values['title'] ?? '');
        }

        if ($type === 'slug' && $value === '' && $title !== '') {
            $value = tamim_slugify($title);
        }

        if ($required && (is_int($value) ? false : trim((string) $value) === '')) {
            $errors[$name] = sprintf('%s is required.', $field['label']);
            continue;
        }

        if ($value === null || $value === '') {
            $values[$name] = null;
            continue;
        }

        if ($value === false || ($type === 'json' && !is_string($value))) {
            $errors[$name] = sprintf('%s is invalid.', $field['label']);
            continue;
        }

        if (is_string($value) && isset($field['max']) && mb_strlen($value) > (int) $field['max']) {
            $errors[$name] = sprintf('%s is too long.', $field['label']);
            continue;
        }

        if (in_array($type, ['integer', 'year'], true) && $value !== null) {
            $minimum = (int) ($field['min'] ?? 0);
            $maximum = (int) ($field['max_value'] ?? 9999);
            if ($type === 'year') {
                $minimum = 1900;
                $maximum = 2100;
            }
            if ($value < $minimum || $value > $maximum) {
                $errors[$name] = sprintf('%s must be between %d and %d.', $field['label'], $minimum, $maximum);
                continue;
            }
        }

        if ($type === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $errors[$name] = 'Enter a valid email address.';
            continue;
        }

        if ($type === 'url' && !admin_valid_url($value)) {
            $errors[$name] = 'Enter a valid HTTP URL or an internal path.';
            continue;
        }

        if ($type === 'slug' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) !== 1) {
            $errors[$name] = 'Use lowercase letters, numbers and single hyphens.';
            continue;
        }

        if ($type === 'select' && !in_array($value, $field['options'], true)) {
            $errors[$name] = sprintf('Select a valid %s.', mb_strtolower($field['label']));
            continue;
        }

        if ($type === 'icon' && preg_match('/^[a-zA-Z0-9_-]+$/', $value) !== 1) {
            $errors[$name] = 'Use letters, numbers, hyphens or underscores.';
            continue;
        }

        if ($type === 'json') {
            try {
                $items = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                if (count($items) > 20 || array_some($items, static fn (mixed $item): bool => !is_string($item) || mb_strlen($item) > 80)) {
                    throw new RuntimeException();
                }
            } catch (Throwable) {
                $errors[$name] = 'Enter one technology per line.';
                continue;
            }
        }

        if ($type === 'slug' && is_string($value)) {
            $statement = $pdo->prepare(sprintf(
                'SELECT id FROM %s WHERE slug = ? AND id <> ?',
                admin_identifier($definition['table'])
            ));
            $statement->execute([$value, $editing ? (int) ($current['id'] ?? 0) : 0]);
            if ($statement->fetchColumn() !== false) {
                $errors[$name] = 'This slug is already in use.';
                continue;
            }
        }

        $values[$name] = $value;
    }

    if (isset($values['current_job']) && (int) $values['current_job'] === 1) {
        $values['end_date'] = null;
    }

    if (isset($values['start_date'], $values['end_date']) && $values['end_date'] !== null && $values['end_date'] < $values['start_date']) {
        $errors['end_date'] = 'End date cannot be before the start date.';
    }

    if (($values['status'] ?? null) === 'published' && empty($values['published_at'])) {
        $values['published_at'] = date('Y-m-d H:i:s');
    }

    return [$values, $errors];
}

function admin_identifier(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function admin_process_uploads(array $definition, ?array $current): array
{
    $values = [];
    $deleteAfterSuccess = [];
    $deleteAfterFailure = [];
    $errors = [];
    $uploadRoot = dirname(__DIR__, 2) . '/assets/uploads';

    foreach ($definition['fields'] as $field) {
        if ($field['type'] !== 'image') {
            continue;
        }

        $name = $field['name'];
        $oldPath = is_array($current) ? ($current[$name] ?? null) : null;
        $remove = isset($_POST['remove_' . $name]) && $_POST['remove_' . $name] === '1';
        $file = $_FILES[$name] ?? null;
        $hasUpload = is_array($file) && isset($file['error']) && (int) $file['error'] !== UPLOAD_ERR_NO_FILE;

        if ($remove && $hasUpload) {
            $errors[$name] = 'Remove the current image or upload a replacement, not both.';
            $values[$name] = $oldPath;
            continue;
        }

        if ($remove) {
            $values[$name] = null;
            if (is_string($oldPath) && $oldPath !== '') {
                $deleteAfterSuccess[] = $oldPath;
            }
            continue;
        }

        if (!$hasUpload) {
            $values[$name] = $oldPath;
            continue;
        }

        try {
            $newPath = tamim_handle_upload($file, $uploadRoot, $field['upload_prefix']);
        } catch (Throwable $error) {
            $errors[$name] = $error->getMessage();
            $values[$name] = $oldPath;
            continue;
        }

        $values[$name] = $newPath;
        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $newPath) {
            $deleteAfterSuccess[] = $oldPath;
        }
        $deleteAfterFailure[] = $newPath;
    }

    return [$values, $deleteAfterSuccess, $deleteAfterFailure, $errors];
}

function admin_delete_files(array $paths): void
{
    foreach ($paths as $path) {
        tamim_delete_upload(is_string($path) ? $path : null);
    }
}

function admin_store_resource(PDO $pdo, array $definition, array $values, ?int $id = null): bool
{
    $table = admin_identifier($definition['table']);
    $columns = array_column($definition['fields'], 'name');
    $parameters = [];

    foreach ($columns as $column) {
        $parameters[':' . $column] = $values[$column] ?? null;
    }

    try {
        $pdo->beginTransaction();
        if ($id === null) {
            $columnSql = implode(', ', array_map(static fn (string $column): string => admin_identifier($column), $columns));
            $placeholderSql = implode(', ', array_map(static fn (string $column): string => ':' . $column, $columns));
            $statement = $pdo->prepare('INSERT INTO ' . $table . ' (' . $columnSql . ') VALUES (' . $placeholderSql . ')');
        } else {
            $setSql = implode(', ', array_map(static fn (string $column): string => admin_identifier($column) . ' = :' . $column, $columns));
            $statement = $pdo->prepare('UPDATE ' . $table . ' SET ' . $setSql . ' WHERE id = :id');
            $parameters[':id'] = $id;
        }
        $statement->execute($parameters);
        $pdo->commit();

        return true;
    } catch (Throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        return false;
    }
}

function admin_fetch_resource(PDO $pdo, array $definition, int $id): ?array
{
    $statement = $pdo->prepare('SELECT * FROM ' . admin_identifier($definition['table']) . ' WHERE id = ?');
    $statement->execute([$id]);
    $row = $statement->fetch();

    return $row ?: null;
}

function admin_delete_resource(PDO $pdo, array $definition, int $id): ?array
{
    $row = admin_fetch_resource($pdo, $definition, $id);
    if ($row === null) {
        return null;
    }

    $statement = $pdo->prepare('DELETE FROM ' . admin_identifier($definition['table']) . ' WHERE id = ?');
    $statement->execute([$id]);

    return $row;
}

function admin_list_resource(PDO $pdo, array $definition, int $page): array
{
    $page = max(1, $page);
    $limit = 25;
    $offset = ($page - 1) * $limit;
    $count = (int) $pdo->query('SELECT COUNT(*) FROM ' . admin_identifier($definition['table']))->fetchColumn();
    $statement = $pdo->prepare('SELECT * FROM ' . admin_identifier($definition['table']) . ' ORDER BY ' . $definition['order'] . ' LIMIT :limit OFFSET :offset');
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();

    return [$statement->fetchAll(), $count, max(1, (int) ceil($count / $limit))];
}

function admin_field_value(array $field, mixed $value): string
{
    if ($value === null) {
        return '';
    }

    if ($field['type'] === 'boolean') {
        return (int) $value === 1 ? '1' : '0';
    }

    if ($field['type'] === 'datetime' && is_string($value) && $value !== '') {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);

        return $date ? $date->format('Y-m-d\TH:i') : '';
    }

    if ($field['type'] === 'json' && is_string($value)) {
        $decoded = json_decode($value, true);

        return is_array($decoded) ? implode("\n", $decoded) : $value;
    }

    return (string) $value;
}

function admin_format_list_value(array $field, mixed $value): string
{
    if ($value === null || $value === '') {
        return '—';
    }

    if ($field['type'] === 'boolean') {
        return (int) $value === 1 ? 'Yes' : 'No';
    }

    if (in_array($field['type'], ['date', 'datetime'], true) && is_string($value)) {
        return tamim_format_date($value, $field['type'] === 'datetime' ? 'M d, Y H:i' : 'M d, Y');
    }

    if ($field['type'] === 'json' && is_string($value)) {
        $decoded = json_decode($value, true);

        return is_array($decoded) ? implode(', ', array_slice($decoded, 0, 3)) : '';
    }

    $text = strip_tags((string) $value);

    return mb_strlen($text) > 90 ? mb_substr($text, 0, 87) . '…' : $text;
}

function admin_resource_config(string $resource): ?array
{
    static $resources = null;
    $resources ??= require __DIR__ . '/resources.php';

    return $resources[$resource] ?? null;
}

function array_some(array $array, callable $callback): bool
{
    foreach ($array as $value) {
        if ($callback($value)) {
            return true;
        }
    }

    return false;
}
