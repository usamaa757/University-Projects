<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

// Check if Lesson ID is provided
if (isset($_GET['lesson_id'])) {
    $lesson_id = $_GET['lesson_id'];

    // Delete the Lesson
    $delete_lesson_stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $delete_lesson_stmt->bind_param("i", $lesson_id);

    if ($delete_lesson_stmt->execute()) {
        $resultMsg = "Lesson deleted successfully.";
    } else {
        $errorMsg = "Error deleting Lesson: " . $conn->error;
    }

    $delete_lesson_stmt->close();

} else {
    $errorMsg = "Lesson ID not provided.";
}

// Close connection
$conn->close();
header("Location: lesson_list.php?resultMsg=" . urlencode($resultMsg) . "&errorMsg=" . urlencode($errorMsg));
