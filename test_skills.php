<?php
require_once 'includes/bootstrap.php';
$skills = skills();
foreach($skills as $s) {
    echo $s['category'] . ' => ' . $s['name'] . PHP_EOL;
}