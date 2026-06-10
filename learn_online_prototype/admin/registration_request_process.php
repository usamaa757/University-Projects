<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if action and ID are set in the POST data
    if (isset($_POST['action']) && isset($_POST['id'])) {
        // Database connection details
        include '../db_connection.php';
        // Initialize error and result messages
        $errorMsg = "";
        $resultMsg = "";

        // Sanitize input to prevent SQL injection
        $action = $_POST['action'];
        $id = $_POST['id'];

        // Update status based on the action

        if ($action === 'approve_student') {
            // Update status to 'approved' for the student
            $sql_approve_student = "UPDATE registration SET status = 'approved' WHERE student_id = $id";
            if ($conn->query($sql_approve_student) === TRUE) {
                $resultMsg = "Student request approved";
            } else {
                $errorMsg = "Error updating student request: " . $conn->error;
            }
        } elseif ($action === 'reject_student') {
            // Update status to 'rejected' for the student
            $sql_reject_student = "UPDATE registration SET status = 'rejected' WHERE student_id = $id";
            if ($conn->query($sql_reject_student) === TRUE) {
                $resultMsg = "Student request rejected";
            } else {
                $errorMsg = "Error rejecting student request: " . $conn->error;
            }
        }

        // Close connection
        $conn->close();

        // Redirect back to the processing page with appropriate messages
        header("Location: registration_request.php");
        exit();
    } else {
        echo "Action and/or ID not provided";
    }
}
