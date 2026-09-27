<?php
require 'C:/xampp/htdocs/Tamim/includes/bootstrap.php';

// Add hero_background_color setting
db_execute('
    INSERT INTO settings (setting_key, setting_value, setting_type, group_name, label, description, sort_order, is_public) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
', [
    'hero_background_color',
    '#DBDBDB',
    'color',
    'hero',
    'Hero Background Color',
    'Background color for the hero section',
    0,
    1
]);

echo "hero_background_color setting added/updated to #DBDBDB\n";