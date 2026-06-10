<?php
include '../db.php';

// Check if the event_id is provided in the URL
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // First, delete related RSVPs from the rsvp_event table
    $delete_rsvp_stmt = $conn->prepare("DELETE FROM rsvps WHERE event_id = ?");
    $delete_rsvp_stmt->bind_param("i", $event_id);
    $delete_rsvp_stmt->execute();

    // Now, delete the event from the events table
    $delete_event_stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $delete_event_stmt->bind_param("i", $event_id);;

    // Check if both deletions were successful
    if ($delete_event_stmt->execute()) {
        header("Location: my_event.php");
        exit();
        // Close the statements

    }
    $delete_rsvp_stmt->close();
    $delete_event_stmt->close();
    $conn->close();
}