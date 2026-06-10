<?php
$conn = new mysqli("localhost", "root", "", "prms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
