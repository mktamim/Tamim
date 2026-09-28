<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

date_default_timezone_set(tamim_env('APP_TIMEZONE', 'Asia/Dhaka'));

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
