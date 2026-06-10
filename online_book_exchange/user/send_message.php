<?php
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the POST data
    $message = $_POST['message'];
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];

    // Check if the message is empty
    if (empty($message)) {
        echo "Message cannot be empty.";
        exit();
    }

    // Insert the new message into the database
    $query = "INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);

    // Bind the parameters
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

    // Execute the query
    if ($stmt->execute()) {
        echo "Message sent successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Invalid request method.";
}
