<?php
// Start the session
session_start();

// Include the database connection file
require '../db_connection.php';

// Check if cloth_id or user_id is set in the URL query
if (isset($_GET['cloth_id']) && isset($_SESSION['user_id'])) {
    $cloth_id = $_GET['cloth_id'];
    $user_id = $_SESSION['user_id'];  // Assuming the user is logged in and user_id is in session

    // Prepare SQL query to delete the item from cart table using mysqli
    $sql = "DELETE FROM cart WHERE cloth_id = ? AND user_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters (integer for both cloth_id and user_id)
        $stmt->bind_param("ii", $cloth_id, $user_id);

        // Execute the query
        if ($stmt->execute()) {
            $_SESSION['message'] = 'cloth removed from cart successfully.';
        } else {
            $_SESSION['message'] = 'Failed to remove cloth from cart.';
        }

        // Close the statement
        $stmt->close();
    } else {
        // If there's an error preparing the statement
        $_SESSION['message'] = 'Error: ' . $conn->error;
    }

    // Redirect to cart page after deletion
    header('Location: cart.php');
    exit;
} else {
    // If cloth_id or user_id is not set, redirect to the cart page
    $_SESSION['message'] = 'Invalid request.';
    header('Location: cart.php');
    exit;
}