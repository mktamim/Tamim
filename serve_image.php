<?php
// Clear any output buffers
while (ob_get_level()) ob_end_clean();

$file = 'C:/xampp/htdocs/Tamim/assets/uploads/settings/fresh_test.jpg';
if (file_exists($file)) {
    header('Content-Type: image/jpeg');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=31536000');
    readfile($file);
} else {
    http_response_code(404);
    echo 'Not found';
}