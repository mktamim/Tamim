<?php
$img = imagecreatetruecolor(200, 200);
for ($x = 0; $x < 200; $x++) {
    for ($y = 0; $y < 200; $y++) {
        $r = (int)($x * 255 / 200);
        $g = (int)($y * 255 / 200);
        $b = 100;
        $color = imagecolorallocate($img, $r, $g, $b);
        imagesetpixel($img, $x, $y, $color);
    }
}
imagejpeg($img, 'C:/xampp/htdocs/Tamim/assets/uploads/settings/fresh_test.jpg', 90);
imagedestroy($img);
echo "Created fresh_test.jpg\n";