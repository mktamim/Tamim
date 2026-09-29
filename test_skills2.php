<?php
require_once 'includes/bootstrap.php';
$skills = skills();
$cats = [];
foreach($skills as $s) {
    $cat = $s['category'] ?? 'other';
    if (!isset($cats[$cat])) $cats[$cat] = [];
    $cats[$cat][] = $s['name'];
}
foreach($cats as $cat => $skills) {
    echo $cat . ': ' . count($skills) . ' skills' . PHP_EOL;
    foreach($skills as $i => $skill) {
        echo '  ' . ($i+1) . '. ' . $skill . PHP_EOL;
    }
}