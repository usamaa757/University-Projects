<?php

include '../../db_connection.php';
if (isset($_GET['quiz_id']) && !empty($_GET['quiz_id']) ) {
    $quiz_id = $_GET['quiz_id'];

    // Direct SQL query to retrieve the subject name of the quiz from the quizzes table
    $sql = "SELECT *  FROM quizzes WHERE quiz_id = $quiz_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $course_id = $row['course_id'];
        $course_name = $row['course_name'];
    } else {
        // Subject name not found
        $course_name = "Subject Name Not Found";
    }
} else {
    // No quiz_id provided in URL
    $course_name = "No Quiz ID Provided";
}

// Initialize variables
$resultMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['quiz_id']) && !empty($_GET['quiz_id'])) {
    $question = $_POST['question'];
    $quiz_id = $_GET['quiz_id']; // Assuming you are passing quiz_id via URL parameter

    // Prepare and bind the SQL statement to insert the question into the questions table
    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $stmt->bind_param("is", $quiz_id, $question);

    $stmt->execute();

    // Get the last inserted question ID
    $question_id = $stmt->insert_id;

    // Prepare and bind the SQL statement to insert options into the options table
    $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $question_id, $option_text, $is_correct);

    // Loop through options 1 to 4
    for ($i = 1; $i <= 4; $i++) {
        $option_text = $_POST['option' . $i];
        $is_correct = ($_POST['correct_answer'] == $i) ? 1 : 0; // Check if this option is correct
        $stmt->execute();
    }

    $resultMsg = "Question added successfully";

    // Close statement
    $stmt->close();
} else {
    // No quiz_id provided or form not submitted via POST request
    $errorMsg = "No Quiz ID Provided or Form Not Submitted";
}

// Close connection
$conn->close();
