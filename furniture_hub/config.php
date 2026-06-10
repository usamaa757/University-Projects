<?php
$host = "localhost";
$user = "root";   // default XAMPP username
$pass = "";       // default XAMPP password
$db   = "furniture_hub";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}