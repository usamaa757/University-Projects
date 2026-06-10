<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cosmetic_store_prototype";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}