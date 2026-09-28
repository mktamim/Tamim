<?php
/**
 * Admin Logout
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../includes/bootstrap.php';

auth_logout();

redirect('/admin/login.php', 'You have been logged out successfully.', 'info');
