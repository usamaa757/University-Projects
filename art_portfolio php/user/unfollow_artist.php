<?php
include '../db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['artist_id'])) {
    $user_id = $_SESSION['user_id'];
    $artist_id = $_GET['artist_id'];

    mysqli_query($conn, "DELETE FROM follows WHERE user_id = '$user_id' AND artist_id = '$artist_id'");
}

header("Location: my_follows.php");