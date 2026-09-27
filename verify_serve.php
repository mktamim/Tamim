<?php
$img = 'C:/xampp/htdocs/Tamim/serve_test.jpg';
echo 'Size: ' . filesize($img) . " bytes\n";
$info = getimagesize($img);
print_r($info);