<?php
include '../db.php';
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role']) == 'artist') {
    header("Location: ../login.php");
    exit();
}
if (!isset($_GET['art_id'])) {
    echo "<script>alert('Art ID not specified.'); window.location.href = 'art_list.php';</script>";
    exit;
}

$art_id = $_GET['art_id'];

if ($conn->query("DELETE FROM arts WHERE art_id = $art_id")) {
    echo "<script>alert('Art deleted successfully.');</script>";
} else {
    echo "<script>alert('Failed to delete art.');</script>";
}

echo "<script>window.location.href = 'art_list.php';</script>";
exit;