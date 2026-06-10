<?php

include '../include_files/db_connection.php';
if (isset($_GET['course_id']) && isset($_SESSION['student_id'])) {
    $course_id = $_GET['course_id'];
    $student_id = $_SESSION['student_id'];

    // Fetch assignments
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch marks for the assignments
    $stmt_marks = $conn->prepare("SELECT * FROM student_assignment WHERE student_id = ? AND course_id = ?");
    $stmt_marks->bind_param("ii", $student_id, $course_id);
    $stmt_marks->execute();
    $result_marks = $stmt_marks->get_result();

    // Store marks in an associative array for quick lookup
    $marks = [];
    while ($row_marks = $result_marks->fetch_assoc()) {
        $marks[$row_marks['assignment_id']] = $row_marks;
    }
    if ($result->num_rows > 0) {
        $assignemnts = array();
        $sn = 1;

        while ($row = $result->fetch_assoc()) {
            $assignemnts[] = $row;
           
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
            $current_date = date('y-m-d');

            $is_open = ($start_date <= $current_date && $current_date <= $end_date);
            $is_before_start = ($current_date < $start_date);
            $is_expired = ($current_date > $end_date);


            // Check if marks for this assignment are available
          
        }
    }
}
