<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if assignment ID, course ID, assignment question, and due date are provided
    if (isset($_POST['assignment_id'], $_POST['course_id'], $_POST['assignment_question'], $_POST['start_date'], $_POST['end_date'])) {
        $assignment_id = $_POST['assignment_id'];
        $course_id = $_POST['course_id'];
        $assignment_question = $_POST['assignment_question'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Update assignment
        $update_stmt = $conn->prepare("UPDATE assignments SET assignment_question = ?, start_date = ?, end_date = ? WHERE assignment_id = ?");
        $update_stmt->bind_param("sssi", $assignment_question, $start_date, $end_date, $assignment_id);

        if ($update_stmt->execute()) {
            $resultMsg = "Assignment updated successfully.";
        } else {
            $errorMsg = "Error updating assignment: " . $conn->error;
        }

        $update_stmt->close();
    } else {
        $errorMsg = "Missing assignment ID, course ID, assignment question, or due date.";
    }
}

// Close connection
$conn->close();
?>
