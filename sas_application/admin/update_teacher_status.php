<?php
// Include database connection
include '../other/db_connection.php';

$message = ""; // Initialize message variable
$messageClass = ""; // Initialize message CSS class

// Check if status update request is submitted via URL parameters
if (isset($_GET['teacher_id']) && isset($_GET['status'])) {
    $teacher_id = $_GET['teacher_id'];
    $status = $_GET['status'];

    // Validate status value
    if (in_array($status, ['approved', 'rejected'])) {
        // Update teacher status in the database
        $update_query = "UPDATE teachers SET status = ? WHERE teacher_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $status, $teacher_id);
        if ($update_stmt->execute()) {
            $message = "Teacher status updated to $status.";
            $messageClass = "success";
        } else {
            $message = "Failed to update status.";
            $messageClass = "error";
        }
        $update_stmt->close();
    } else {
        $message = "Invalid status value.";
        $messageClass = "error";
    }
} else {
    $message = "Required parameters missing.";
    $messageClass = "error";
}

// Close database connection
$conn->close();

// Redirect back to the teachers list page with a message
header("Location: list_teachers.php?message=" . urlencode($message) . "&messageClass=" . urlencode($messageClass));
exit();
?>
