<?php

// Database connection
include '../db_connection.php';

$user_id = $_SESSION['user_id'];

// Query to fetch unread notifications for the logged-in user
$query = "SELECT * FROM notifications WHERE user_id = '$user_id' AND is_read = 0 ORDER BY timestamp DESC";
$result = mysqli_query($conn, $query);

// Fetch the notifications into an array
$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}

// Get the count of unread notifications
$unread_count = count($notifications);
