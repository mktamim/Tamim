<?php
header('Content-Type: application/json');
echo json_encode([
    'cookies' => $_COOKIE,
    'raw_cookie_header' => $_SERVER['HTTP_COOKIE'] ?? 'NOT_SET',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'NOT_SET'
]);