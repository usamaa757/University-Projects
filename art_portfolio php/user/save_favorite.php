<?php
include '../db.php';
session_start();
$user_id = $_SESSION['user_id'];
$artwork_id = $_GET['artwork_id'];

$check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = '$user_id' AND artwork_id = '$artwork_id'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO favorites (user_id, artwork_id) VALUES ('$user_id', '$artwork_id')");
}
header("Location: view_gallery.php");