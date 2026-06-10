<?php
$host = 'localhost'; // Your database host
$user = 'root'; // Your database username
$password = ''; // Your database password
$dbname = 'if0_37623875_lms'; // Your database name

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>