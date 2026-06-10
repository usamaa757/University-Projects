<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

// Check if lesson ID is provided
if (isset($_GET['lesson_id'])) {
    $lesson_id = $_GET['lesson_id'];

    // Fetch lesson details from the database
    $stmt = $conn->prepare("SELECT file_name, title, file_type, course_id, start_date, end_date FROM lessons WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $stmt->bind_result($file_name, $title, $file_type, $course_id, $start_date, $end_date);
    $stmt->fetch();
    $stmt->close();

    // Construct the video URL based on file name and file type
    $video_url = "videos/" . $file_name;
} else {
    $errorMsg = "Lesson ID not provided.";
}
?>