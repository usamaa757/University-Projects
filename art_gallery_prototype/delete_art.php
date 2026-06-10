<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) == 'seller') {
    header("Location: login.php");
}

if (isset($_GET['art_id'])) {
    $art_id = $_GET['art_id'];
    $delete_query = "DELETE FROM art_items WHERE art_id='$art_id'";

    if ($conn->query($delete_query)) {
        echo "<script>alert('Art deleted successfully!'); window.location='view_art.php';</script>";
    } else {
        echo "<script>alert('Failed to delete. Try again!');</script>";
    }
}