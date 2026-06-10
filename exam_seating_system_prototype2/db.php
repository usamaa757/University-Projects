<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "exam_arrangement_system_prototype";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}