<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Use your database password
$dbname = "chairperson_selection";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
