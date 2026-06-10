<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";
$assignment_question = "";
$due_date = "";

// Check if assignment ID is provided
if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    // Fetch assignment details from the database
    $stmt = $conn->prepare("SELECT assignment_question, start_date, end_date FROM assignments WHERE assignment_id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $stmt->bind_result($assignment_question, $start_date, $end_date);
    $stmt->fetch();
    $stmt->close();
} else {
    $errorMsg = "Assignment ID not provided.";
}
?>