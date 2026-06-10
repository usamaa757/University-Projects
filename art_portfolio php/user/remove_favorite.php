<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_id'])) {
    $favorite_id = $_POST['favorite_id'];
    $user_id = $_SESSION['user_id'];

    // Only delete if it belongs to the logged-in user
    mysqli_query($conn, "DELETE FROM favorites WHERE id = '$favorite_id' AND user_id = '$user_id'");
}

header("Location: my_favorites.php");