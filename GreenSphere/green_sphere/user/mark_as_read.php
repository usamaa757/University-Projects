<?php
include '../db_connection.php';  // Include database connection

$notification_id = $_GET['notification_id'];  // Notification ID

// Query to mark the notification as read
$update_query = "UPDATE notifications SET is_read = 1 WHERE notification_id = '$notification_id'";

if (mysqli_query($conn, $update_query)) {
    echo "Notification marked as read.";
    // Redirect back to the notifications page or user dashboard
    header("Location: dashboard.php");
} else {
    echo "Failed to mark notification as read.";
}
