<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: ../../login/student_login.php");
    exit();
}

include_once '../include_files/db_connection.php';

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
$student_id = $_SESSION['student_id'];
$current_date = date('Y-m-d');

// Fetch assignments from the database for the given course
$query_assignments = "SELECT a.*, c.course_name 
                      FROM assignments a 
                      INNER JOIN courses c ON a.course_id = c.course_id 
                      WHERE a.course_id = ?";
$stmt_assignments = $conn->prepare($query_assignments);
$stmt_assignments->bind_param('i', $course_id);
$stmt_assignments->execute();
$result_assignments = $stmt_assignments->get_result();

$assignments = [];
if ($result_assignments->num_rows > 0) {
    while ($row = $result_assignments->fetch_assoc()) {
        // Check if the end_date is greater than or equal to the current date
        if ($row['end_date'] >= $current_date) {
            $assignments[] = $row;
        }
    }
}

$conn->close();
?>