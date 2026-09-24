<?php
function tamim_url(?string $url): string
{
    if ($url === null || trim($url) === '') {
        return '#';
    }

    $url = trim($url);
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return $url;
    }

    return preg_match('/^https?:\/\//i', $url) === 1 && filter_var($url, FILTER_VALIDATE_URL) !== false ? $url : '#';
}

function tamim_json_array(string $value): array
{
    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : [$value];
}

function tamim_excerpt(string $value, int $length = 180): string
{
    $value = trim(preg_replace('/\s+/', ' ', strip_tags($value)) ?? $value);
    return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 1) . '…' : $value;
}
