<?php
require 'C:/xampp/htdocs/Tamim/includes/bootstrap.php';
$r = db_all('SELECT * FROM settings WHERE group_name = "hero"');
print_r($r);