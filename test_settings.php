<?php
require_once 'includes/bootstrap.php';
$url = setting('site_url');
echo 'site_url: ' . ($url ?? 'NULL') . PHP_EOL;
$profile = setting('profile_image');
echo 'profile_image: ' . ($profile ?? 'NULL') . PHP_EOL;