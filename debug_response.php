<?php
$ch = curl_init('http://localhost/Tamim/serve_image');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

echo "=== HEADERS ===\n$headers\n";
echo "=== BODY (first 50 bytes hex) ===\n" . bin2hex(substr($body, 0, 50)) . "\n";
echo "=== BODY length ===\n" . strlen($body) . "\n";
curl_close($ch);