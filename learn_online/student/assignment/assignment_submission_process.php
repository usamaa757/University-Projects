<?php
include '../../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_SESSION['student_id'];

    $assignment_id = $_POST['assignment_id'];
    $course_id = $_POST['course_id'];
  
  
    $assignment_answer = $_POST['assignment_answer'];
    $status = 'Submitted';

    // Check if an assignment already exists for the student and subject
    $sql_check = "SELECT * FROM student_assignment WHERE course_id = ? AND assignment_id = ? AND student_id=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iii", $course_id, $assignment_id, $student_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Assignment already exists, update it
        $sql = "UPDATE student_assignment SET assignment_answer = ?, status = ? WHERE course_id = ? AND student_id = ? AND assignment_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $assignment_answer, $status, $course_id, $student_id, $assignment_id);
        
        if ($stmt->execute()) {
            $resultMsg = "Assignment updated successfully!";
        } else {
            $errorMsg = "Error: Assignment update failed!";
        }
    } else {
        // Assignment does not exist, insert a new one
        $sql = "INSERT INTO student_assignment (assignment_id, student_id, course_id, assignment_answer, status) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $assignment_id, $student_id, $course_id, $assignment_answer, $status);
        
        if ($stmt->execute()) {
            $resultMsg = "Assignment submitted successfully!";
        } else {
            $errorMsg = "Error: Assignment submission failed!";
        }
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>
