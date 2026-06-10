<?php
session_start();
include '../db_connection.php';

if (isset($_POST['book_id'], $_POST['user_book'], $_POST['requested_to'], $_POST['requested_by'])) {
    $book_id = $_POST['book_id'];
    $user_book_id = $_POST['user_book'];
    $requested_to = $_POST['requested_to'];
    $requested_by = $_POST['requested_by'];

    // Insert the exchange request into the database
    $stmt = $conn->prepare("INSERT INTO exchange_requests (book_id, user_book_id, requested_by, requested_to, request_date) 
                            VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiii", $book_id, $user_book_id, $requested_by, $requested_to);

    if ($stmt->execute()) {
        header("Location: books_list.php?msg=Exchange request sent successfully!");
    } else {
        header("Location: books_list.php?error=Failed to send exchange request.");
    }
    exit();
} else {
    header("Location: books_list.php?error=Invalid request.");
    exit();
}
