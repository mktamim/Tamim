<?php
$img = 'C:/xampp/htdocs/Tamim/fresh_downloaded.jpg';
echo 'Size: ' . filesize($img) . " bytes\n";
$info = getimagesize($img);
print_r($info);