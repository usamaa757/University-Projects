<?php
// Include database connection
include '../other/db_connection.php';

$message = ""; // Initialize message variable
$messageClass = ""; // Initialize message CSS class

// Check if status update request is submitted via URL parameters
if (isset($_GET['student_id']) && isset($_GET['status'])) {
    $student_id = $_GET['student_id'];
    $status = $_GET['status'];

    // Validate status value
    if (in_array($status, ['approved', 'rejected'])) {
        // Update student status in the database
        $update_query = "UPDATE students SET status = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $status, $student_id);
        if ($update_stmt->execute()) {
            $message = "Student status updated to $status.";
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

// Redirect back to the students list page with a message
header("Location: list_students.php?message=" . urlencode($message) . "&messageClass=" . urlencode($messageClass));
exit();
?>
