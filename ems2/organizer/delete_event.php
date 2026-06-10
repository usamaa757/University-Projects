<?php

include '../db.php';

// Check if the event_id is provided in the URL
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Delete the event from the database
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event deleted successfully!'); window.location.href='view_events.php';</script>";
    } else {
        echo "<script>alert('Error deleting event: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Event ID is required'); window.location.href='view_events.php';</script>";
}