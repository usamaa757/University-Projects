<?php
include '../db_connection.php';

if (isset($_GET['action']) && isset($_GET['candidate_id'])) {
    $action = $_GET['action'];
    $candidate_id = $_GET['candidate_id'];

    // Prepare the update query based on action
    if ($action == 'approve') {
        $status = 'Approved';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    } else {
        die("Invalid action.");
    }

    $stmt = $conn->prepare("UPDATE candidates SET status = ? WHERE candidate_id = ?");
    $stmt->bind_param("si", $status, $candidate_id);

    if ($stmt->execute()) {
        $message = "Candidate status updated successfully.";
    } else {
        $message = "Failed to update candidate status.";
    }

    $stmt->close();
    $conn->close();
    header("Location: candidate_reg_request.php?message=" . urlencode($message));
    exit();
}
?>
