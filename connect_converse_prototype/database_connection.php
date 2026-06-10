<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "connect_converse_prototype";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}