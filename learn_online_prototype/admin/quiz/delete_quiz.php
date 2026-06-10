<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

// Check if quiz ID is provided
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];

    // Delete options related to the quiz
    $delete_options_stmt = $conn->prepare("DELETE FROM options WHERE question_id IN (SELECT question_id FROM questions WHERE quiz_id = ?)");
    $delete_options_stmt->bind_param("i", $quiz_id);
    $delete_options_stmt->execute();

    // Delete questions related to the quiz
    $delete_questions_stmt = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $delete_questions_stmt->bind_param("i", $quiz_id);
    $delete_questions_stmt->execute();

    // Delete the quiz
    $delete_quiz_stmt = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
    $delete_quiz_stmt->bind_param("i", $quiz_id);

    if ($delete_quiz_stmt->execute()) {
        $resultMsg = "Quiz deleted successfully.";
    } else {
        $errorMsg = "Error deleting Quiz: " . $conn->error;
    }

    $delete_quiz_stmt->close();
    $delete_questions_stmt->close();
    $delete_options_stmt->close();
} else {
    $errorMsg = "Quiz ID not provided.";
}

// Close connection
$conn->close();
header("Location: quiz_record.php?resultMsg=" . urlencode($resultMsg) . "&errorMsg=" . urlencode($errorMsg));
