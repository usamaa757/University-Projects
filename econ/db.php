<?php
$host = 'localhost';
$db = 'earthcon';
$user = 'root';
$pass = '';

// Create DB connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}