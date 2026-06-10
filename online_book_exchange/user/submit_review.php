<?php
include("../db_connection.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $requested_to_id = intval($_POST['requested_to']);
    $book_id = intval($_POST['book_id']);
    $request_id = intval($_POST['request_id']);
    $rating = intval($_POST['rating']);
    $review_text = $_POST['review_text']; // Treat as a string
    $user_id = $_SESSION['user_id'];

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        echo "Invalid rating value.";
        exit();
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO exchange_reviews (requested_to_id, book_id, requested_by_id, request_id, rating, review_text, review_date) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    // Bind the parameters (note: review_text is now bound as a string 's')
    $stmt->bind_param("iiiiss", $requested_to_id, $book_id, $user_id, $request_id, $rating, $review_text);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: request_book_status.php?msg=" . urlencode("Review submitted successfully."));
    } else {
        // Handle error and redirect with error message
        $error = "Error submitting review: " . $stmt->error;
        header("Location: request_book_status.php?error=" . urlencode($error));
    }

    // Close the prepared statement and the connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
