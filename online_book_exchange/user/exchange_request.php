<?php
session_start();
include '../db_connection.php';

if (isset($_GET['book_id']) && isset($_GET['user_id'])) {
    $book_id = $_GET['book_id'];
    $requested_to = $_GET['user_id'];
    $requested_by = $_SESSION['user_id']; // Current user ID

    // Insert the exchange request into the database
    $stmt = $conn->prepare("INSERT INTO exchange_requests (book_id, requested_by, requested_to) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $book_id, $requested_by, $requested_to);

    if ($stmt->execute()) {
        header("Location: books_list.php?msg=Request sent successfully!");
    } else {
        header("Location: books_list.php?error=Failed to send request.");
    }
    exit();
} else {
    header("Location: books_list.php?error=Invalid request.");
    exit();
}
