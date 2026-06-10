<?php
// Database credentials
$host = 'localhost';  // Usually 'localhost'
$dbname = 'college_admission';  // Your database name
$username = 'root';  // Your database username, usually 'root'
$password = '';  // Your database password, usually empty for 'root' on localhost

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
