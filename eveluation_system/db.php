<?php
// Database connection settings
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "eveluation_system";

// Create MySQLi connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");