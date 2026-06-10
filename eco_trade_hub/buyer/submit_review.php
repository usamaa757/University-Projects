<?php
include("../db_connection.php");
session_start();

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seller_id = intval($_POST['seller_id']);
    $buyer_id = intval($_POST['buyer_id']);
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5) {
        echo "Invalid rating value.";
        exit();
    }

    $sql = "INSERT INTO reviews (seller_id, buyer_id, rating, review_text, review_date) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $seller_id, $buyer_id, $rating, $review_text);

    if ($stmt->execute()) {
        $msg = "Review submitted successfully.";
    } else {
        $error = "Error submitting review: ";
    }
    header("Location: display_review.php?seller_id=" . $seller_id . "&msg=" . urlencode("Review submitted successfully."));
} else {
    echo "Invalid request method.";
}
?>
