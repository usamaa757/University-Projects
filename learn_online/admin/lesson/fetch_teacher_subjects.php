<?php
// Include the database connection script
include '../../common_process/db_connection.php';

// Check if the teacher ID is set in the session
if (isset($_SESSION['teacher_id'])) {
    // Retrieve the teacher ID from the session
    $teacher_id = $_SESSION['teacher_id'];

    // Fetch subjects associated with the teacher from the teacher_subjects table
    $sql = "SELECT subjects.subject_id, subjects.subject_name, class_subjects.class_id
            FROM teacher_subjects
            INNER JOIN subjects ON teacher_subjects.subject_id = subjects.subject_id
            INNER JOIN class_subjects ON teacher_subjects.subject_id = class_subjects.subject_id
            WHERE teacher_subjects.teacher_id = '$teacher_id'";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($subject = $result->fetch_assoc()) {
            $teacher_subjects[] = $subject;
            $_SESSION['class_id']=$subject['class_id'];
           
        }
    } else {
        $resultMsg = "No subjects found for this teacher.";
    }
} else {
    $errorMsg = "Teacher ID not provided.";
}

// Close connection
$conn->close();
?>
