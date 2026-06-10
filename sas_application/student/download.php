<?php
// Include database connection file if needed
// require '../other/db_connection.php';

// Path to the directory where files are stored
$upload_dir = '../teacher/uploads/';

// Get the file parameter from the query string
$filename = isset($_GET['file']) ? basename($_GET['file']) : '';

// Sanitize the file name to prevent directory traversal attacks
$filepath = $upload_dir . $filename;

// Check if the file exists
if (file_exists($filepath)) {
    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    
    // Clear output buffering
    ob_clean();
    flush();
    
    // Read the file and output its contents
    readfile($filepath);
    exit;
} else {
    // If file not found, display an error message
    echo 'File not found.';
}
?>
