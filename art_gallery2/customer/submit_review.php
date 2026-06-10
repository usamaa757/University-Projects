<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $art_id = $_POST['art_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, art_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $art_id, $rating, $review_text);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for your review!'); window.location='art_details.php?art_id=$art_id';</script>";
    } else {
        echo "<script>alert('Error submitting review.'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
