<?php
include '../other/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $course_id = $_POST['course_id'];

    if ($action == 'delete') {
        // Prepare the delete statement
        $delete_query = "DELETE FROM courses WHERE course_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param('i', $course_id);
        $result = $stmt->execute();

        // Check if the course was deleted successfully
        if ($result) {
            echo "Course deleted successfully.";
        } else {
            echo "Error deleting course.";
        }

        // Close the statement and connection
        $stmt->close();
    }
}
$conn->close();
?>