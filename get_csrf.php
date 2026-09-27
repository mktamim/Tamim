<?php
require 'C:/xampp/htdocs/Tamim/includes/bootstrap.php';

echo "CSRF Token: " . csrf_token() . "\n";
echo "Session ID: " . session_id() . "\n";