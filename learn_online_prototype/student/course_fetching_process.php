<?php
session_start(); // Make sure to start the session if not already started

include '../db_connection.php';

// Assuming you have a session variable for student_id
$student_id = $_SESSION["student_id"];

// Query to fetch the selected courses for the logged-in student
$sql_courses = "SELECT c.course_id, c.course_name
                FROM student_course sc
                JOIN courses c ON sc.course_id = c.course_id
                WHERE sc.student_id = ?";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->bind_param("i", $student_id);
$stmt_courses->execute();
$result_courses = $stmt_courses->get_result();

// Store courses in an array
$courses = [];
while ($row_course = $result_courses->fetch_assoc()) {
    $courses[] = $row_course;
}

// Close prepared statement and database connection
$stmt_courses->close();
$conn->close();

?>
