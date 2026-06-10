<?php
session_start(); // Start session

include "../db_connection.php";

if (isset($_GET['question_id']) && isset($_GET['exam_id']) && isset($_GET['subject_id'])) {
    $question_id = $_GET['question_id'];
    $exam_id = $_GET['exam_id'];
    $subject_id = $_GET['subject_id'];

    // Perform the delete operation
    $query = "DELETE FROM questions WHERE exam_id = ? AND question_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $exam_id, $question_id);

    if ($stmt->execute()) {
        // Set success message
        $_SESSION['success'] = "Question deleted successfully!";
    } else {
        // Set error message
        $_SESSION['error'] = "Error deleting question: " . $stmt->error;
    }
    // Redirect back to the main page
    header("Location: edit_questions.php?exam_id=" . $exam_id . "&subject_id=" . $subject_id);
    exit;
} else {
    $_SESSION['error'] = "Invalid exam ID!";
    header("Location: edit_questions.php?exam_id=" . $exam_id . "&subject_id=" . $subject_id);
    exit;
}