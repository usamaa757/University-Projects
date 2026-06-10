<?php
session_start();
include '../db_connection.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    // Prepare the update query based on action
    if ($action == 'approve') {
        $status = 'approved';
    } elseif ($action == 'reject') {
        $status = 'rejected';
    } else {
        die("Invalid action.");
    }

    // Update the registration status
    $stmt = $conn->prepare("UPDATE voter_registration SET registration_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "<script>
        alert('Registration status updated successfully.');
            window.location.href = 'registration_request.php';
        </script>";

    } else {
     echo "<script>
        alert('Failed to update registration status.');
            window.location.href = 'registration_request.php';
        </script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to admin page
    header('Location: voter_reg_request.php');
    exit;
} else {
    die("Invalid request.");
}
