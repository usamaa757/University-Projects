<?php

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "vu_proctors_diary";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}