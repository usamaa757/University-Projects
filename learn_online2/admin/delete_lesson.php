<?php
include '../include_files/db_connection.php';

$errorMsg = "";
$resultMsg = "";

if (isset($_GET['lesson_id']) && isset($_GET['course_id'])) {
    $lesson_id = $_GET['lesson_id'];
    $course_id = $_GET['course_id'];

    $delete_lesson_stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $delete_lesson_stmt->bind_param("i", $lesson_id);

    if ($delete_lesson_stmt->execute()) {
        echo "<script>alert('Lesson deleted successfully.'); window.location.href='lesson_list.php?course_id=" . $course_id . "';</script>";
    } else {
        echo "<script>alert('Error deleting lesson: " . $conn->error . "'); window.location.href='lesson_list.php?course_id=" . $course_id . "';</script>";
    }

    $delete_lesson_stmt->close();

} else {
    echo "<script>alert('Lesson ID or Course ID not provided.'); window.location.href='lesson_list.php';</script>";
}

$conn->close();
?>
