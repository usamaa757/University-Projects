<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "career_hub_prototype";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
