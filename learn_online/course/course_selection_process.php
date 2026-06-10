<?php
include '../common_process/db_connection.php';
include '../common_process/db_connection.php';
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["selected_subject"]) && isset($_SESSION['class_id'])) {
        // Retrieve class_id from session
        $class_id = $_SESSION['class_id'];
      
        // Retrieve selected subjects
        $selected_subjects = $_POST["selected_subject"];
        $subject_names = $_POST["subject_name"]; 
        
        $student_id = $_SESSION["student_id"]; 
        $student_name = $_SESSION["student_name"]; 
        foreach ($selected_subjects as $key => $subject_id) {
            // Check if the student is already associated with the subject
            $stmt_check = $conn->prepare("SELECT * FROM student_subjects WHERE student_id = ? AND subject_id = ?");
            $stmt_check->bind_param("ii", $student_id, $subject_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // Subject is already associated with the student
                $errorMsg = "Subject already selected.";
            } else {
                // Insert into student_subjects table
                $stmt_insert = $conn->prepare("INSERT INTO student_subjects (student_name, class_id, student_id, subject_id, subject_name) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("siiis", $student_name, $class_id, $student_id, $subject_id, $subject_names[$key]); // Use $key to access the corresponding subject name
                $stmt_insert->execute();
                $stmt_insert->close();
            }

            $stmt_check->close();
        }

        if (!isset($errorMsg)) {
            // Redirect or display success message after insertion
            // header("Location: success.php");
            // exit();
            $resultMsg = "Subjects have successfully inserted.";
        }
    } else {
        // No subjects selected
        $errorMsg = "Please select at least one subject.";
    }
}
