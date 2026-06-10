<?php
session_start();
include_once '../../db_connection.php';

// Check if the quiz ID is provided in the GET method
if (!isset($_GET['quiz_id'])) {
    echo "Quiz ID not provided.";
    exit();
}

$quiz_id = $_GET['quiz_id'];

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "Student not logged in.";
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the total number of questions in the quiz
$query_count = "SELECT COUNT(*) AS total_questions FROM questions WHERE quiz_id = ?";
$statement_count = $conn->prepare($query_count);
$statement_count->bind_param('i', $quiz_id);
$statement_count->execute();
$result_count = $statement_count->get_result();

// Check if the query is successful
if ($statement_count->errno) {
    echo "Error fetching question count: " . $statement_count->error;
    $statement_count->close();
    exit();
}

// Fetch the total question count from the result
if ($row_count = $result_count->fetch_assoc()) {
    $total_questions = $row_count['total_questions'];
} else {
    $total_questions = 0; // Handle cases where no questions are found
}

// Calculate total time allowed (1 minute per question)
$total_time_allowed = $total_questions * 60; // in seconds

// Fetch the questions for the quiz
$query_questions = "
    SELECT q.question_id, q.question_text, o.option_id, o.option_text
    FROM questions q
    INNER JOIN options o ON q.question_id = o.question_id
    WHERE q.quiz_id = ?";
$statement_questions = $conn->prepare($query_questions);
$statement_questions->bind_param('i', $quiz_id);
$statement_questions->execute();
$result_questions = $statement_questions->get_result();

// Check if any questions are found for the quiz
if ($result_questions->num_rows > 0) {
    // Initialize an array to store questions and options
    $questions = [];

    // Loop through each row (question) in the result set
    while ($row_question = $result_questions->fetch_assoc()) {
        $question_id = $row_question['question_id'];
        $question_text = $row_question['question_text'];

        // Add the question to the array if it's not already added
        if (!isset($questions[$question_id])) {
            $questions[$question_id] = [
                'question_id' => $question_id,
                'question_text' => $question_text,
                'options' => []
            ];
        }

        // Add the option to the question
        $questions[$question_id]['options'][] = [
            'option_id' => $row_question['option_id'],
            'option_text' => $row_question['option_text']
        ];
    }
} else {
    echo "No questions found for the quiz.";
    exit();
}

// Display quiz questions and options (for debugging or frontend integration)
foreach ($questions as $question) {
    echo "Question: " . $question['question_text'] . "<br>";
    foreach ($question['options'] as $option) {
        echo "Option: " . $option['option_text'] . "<br>";
    }
    echo "<br>";
}

$statement_count->close();
$statement_questions->close();
$conn->close();
?>
