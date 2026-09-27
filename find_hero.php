<?php
require 'C:/xampp/htdocs/Tamim/includes/bootstrap.php';
$r = db_all('SELECT setting_key, setting_value, label, group_name FROM settings WHERE setting_key LIKE "%hero%" OR setting_key LIKE "%background%" OR label LIKE "%Hero%" OR label LIKE "%Background%"');
print_r($r);