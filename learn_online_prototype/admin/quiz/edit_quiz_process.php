<?php
 include '../../db_connection.php';

 $errorMsg = "";
 $resultMsg = "";
 $quiz_id = "";
 $due_date = "";

 // Check if quiz ID is provided
 if (isset($_GET['quiz_id'])) {
     $quiz_id = $_GET['quiz_id'];

     // Fetch quiz details from the database
     $stmt = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
     $stmt->bind_param("i", $quiz_id);
     $stmt->execute();
     $result = $stmt->get_result();

     $quiz_details = array();

     while ($row = $result->fetch_assoc()) {
         $quiz_details[] = $row;
         $subject_id = $row['course_id'];
     }

     $stmt->close();
 } else {
     $errorMsg = "Quiz ID not provided.";
 }