<?php
$content = file_get_contents('C:/xampp/htdocs/Tamim/test_all_images.html');
echo 'Length: ' . strlen($content) . PHP_EOL;
echo 'First 20 bytes: ' . bin2hex(substr($content, 0, 20)) . PHP_EOL;
echo 'Encoding: ' . mb_detect_encoding($content) . PHP_EOL;