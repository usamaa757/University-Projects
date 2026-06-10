<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "cosmetic_store";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}