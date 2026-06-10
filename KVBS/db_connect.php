<?php
$host = "localhost";
$user = "root";     // your MySQL username
$pass = "";         // your MySQL password
$db   = "kvbs";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}