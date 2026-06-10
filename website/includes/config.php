<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'academic_portal');

// File upload configuration
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'exe', 'msi', 'jpg', 'png', 'txt']);
define('UPLOAD_PATH', 'uploads/');

// Website configuration
define('SITE_NAME', 'Academic Resource Portal');
define('SITE_URL', 'http://localhost/academic-portal/');

// Start session
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>