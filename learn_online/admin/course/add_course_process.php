<?php
include '../../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $course_name = $_POST['course_name'];

    // Check if course name already exists
    $checkStmt = $conn->prepare("SELECT course_name FROM courses WHERE course_name = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("s", $course_name);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $error = "Course already exists.";
            header("Location: course_management.php?errorMsg=" . urlencode($error));
            exit();
        }
        $checkStmt->close();
    } else {
        $error= "Error preparing the check statement.";
        header("Location: course_management.php?errorMsg=" . urlencode($error));
        exit();
    }

    // Insert course into the database
    $stmt = $conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $course_name);
        if ($stmt->execute()) {
            $stmt->close();
            $result = "Course inserted successfully!";
            header("Location: course_management.php?resultMsg=" . urlencode($result));
            exit();
        } else {
            $error = "Error inserting course.";
            header("Location: course_management.php?errorMsg=" . urlencode($error));
            exit();
        }
    } else {
        $error = "Error preparing the statement.";
        header("Location: course_management.php?errorMsg=" . urlencode($error));
        exit();
    }
}
?>
