<?php
include '../db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $artwork_id = $_POST['artwork_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        mysqli_query($conn, "INSERT INTO comments (user_id, artwork_id, content) VALUES ('$user_id', '$artwork_id', '$content')");
    }
}
header("Location: view_gallery.php");