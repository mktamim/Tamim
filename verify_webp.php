<?php
$img = 'C:/xampp/htdocs/Tamim/test_display.webp';
echo 'Size: ' . filesize($img) . " bytes\n";
$info = getimagesize($img);
print_r($info);

// Try to load and display info
$im = imagecreatefromwebp($img);
if ($im) {
    echo "Valid WebP - " . imagesx($im) . "x" . imagesy($im) . "\n";
    imagedestroy($im);
} else {
    echo "Invalid WebP\n";
}