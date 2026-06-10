<?php
include '../db.php';
session_start();
$user_id = $_SESSION['user_id'];
$artist_id = $_GET['artist_id'];

if ($user_id != $artist_id) {
    $check = mysqli_query($conn, "SELECT * FROM follows WHERE user_id = '$user_id' AND artist_id = '$artist_id'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO follows (user_id, artist_id) VALUES ('$user_id', '$artist_id')");
    }
}
header("Location: view_gallery.php");