<?php
$img = 'C:/xampp/htdocs/Tamim/assets/uploads/settings/fresh_test.jpg';
echo 'File size: ' . filesize($img) . PHP_EOL;
$h = fopen($img, 'rb');
$header = fread($h, 10);
fclose($h);
echo 'First 10 bytes: ' . bin2hex($header) . PHP_EOL;