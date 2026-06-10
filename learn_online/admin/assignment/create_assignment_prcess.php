<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if course ID, course name, assignment question, and due date are provided
    if (isset($_POST['course_id'], $_POST['assignment_question'], $_POST['start_date'], $_POST['end_date'])) {
        $course_id = $_POST['course_id'];
       
        $assignment_question = $_POST['assignment_question'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
       
        // Convert assignment question to string if it's not already
        if (!is_string($assignment_question)) {
            $assignment_question_str = implode(', ', $assignment_question);
        } else {
            $assignment_question_str = $assignment_question;
        }

        // Insert assignment
        $insert_stmt = $conn->prepare("INSERT INTO assignments (course_id, assignment_question, start_date, end_date) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("isss", $course_id, $assignment_question_str, $start_date, $end_date);

        if ($insert_stmt->execute()) {
            $resultMsg = "Assignment uploaded successfully.";
        } else {
            $errorMsg = "Error uploading assignment: " . $conn->error;
        }

        $insert_stmt->close();
    } else {
        $errorMsg = "Missing course ID, course name, assignment question, due date, or class ID.";
    }
}


?>
