<?php
include '../../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];

    // Update course in the database
    $stmt = $conn->prepare("UPDATE courses SET course_name = ? WHERE course_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $course_name, $course_id);
        if ($stmt->execute()) {
            $stmt->close();
            $resultMsg = "Course updated successfully!";
            header("Location: course_management.php?result=" . urlencode($resultMsg));
            exit();
        } else {
            $errorMsg = "Error updating course.";
            header("Location: course_management.php?error=" . urlencode($errorMsg));
            exit();
        }
    } else {
        $errorMsg = "Error preparing the statement.";
        header("Location: course_management.php?error=" . urlencode($errorMsg));
        exit();
    }
}
?>
