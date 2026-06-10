<?php
$host = 'sql207.infinityfree.com'; // Your database host
$user = 'if0_37623875'; // Your database username
$password = 'Usama757'; // Your database password
$dbname = 'if0_37623875_lms'; // Your database name

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>