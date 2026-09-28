<?php
header('Content-Type: application/json');
echo json_encode([
    'cookies' => $_COOKIE,
    'headers' => getallheaders(),
    'server_https' => $_SERVER['HTTPS'] ?? 'not set',
    'server_http_host' => $_SERVER['HTTP_HOST'] ?? 'not set'
]);