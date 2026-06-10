<?php

include '../db_connection.php';

if (isset($_GET['book_id']) && isset($_GET['exchange'])) {
    $book_id = $_GET['book_id'];

    // Update the book listing status to 'exchanged'
    $stmt = $conn->prepare("UPDATE books SET status = 'exchanged' WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
} else {
    $book_id = $_GET['book_id'];

    $stmt = $conn->prepare("UPDATE books SET status = 'available' WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
}
if ($stmt->execute()) {
    header("Location: my_books.php?msg=Book status changed.");
    exit();
} else {
    header("Location: my_books.php?error=Error updating status.");
    exit();
}
