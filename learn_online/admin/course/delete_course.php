<?php
include '../../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Delete related records from lessons table
        $stmt = $conn->prepare("DELETE FROM lessons WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $affectedRowsLessons = $stmt->affected_rows;
        $stmt->close();

        // Delete related records from quizzes table
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $affectedRowsQuizzes = $stmt->affected_rows;
        $stmt->close();

        // Delete related records from assignments table
        $stmt = $conn->prepare("DELETE FROM assignments WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $affectedRowsAssignments = $stmt->affected_rows;
        $stmt->close();

        // Check if course exists in courses table before deleting
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $affectedRowsCourses = $stmt->affected_rows;
        $stmt->close();

        // If no records were deleted from the courses table, rollback the transaction
        if ($affectedRowsCourses == 0) {
            throw new Exception("Course ID not found in the courses table.");
        }

        // Commit transaction
        $conn->commit();

        $resultMsg = "Course and related records deleted successfully!";
        header("Location: course_management.php?result=" . urlencode($resultMsg));
        exit();
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();

        $errorMsg = "Error deleting course: " . $e->getMessage();
        header("Location: course_management.php?error=" . urlencode($errorMsg));
        exit();
    }
}
?>
