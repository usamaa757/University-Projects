<?php

include '../include_files/db_connection.php';


// Get the student ID from the session
$student_id = $_SESSION['student_id'];

// Query to fetch the course ID associated with the student
$query_course = "SELECT course_id FROM student_course WHERE student_id = ?";
$stmt_course = $conn->prepare($query_course);
$stmt_course->bind_param('i', $student_id);
$stmt_course->execute();
$result_course = $stmt_course->get_result();

// Initialize arrays for course IDs and quizzes
$course_ids = [];
$quizzes = [];

// Check if there are courses associated with the student
if ($result_course->num_rows > 0) {
    // Loop through each course associated with the student
    while ($row_course = $result_course->fetch_assoc()) {
        $course_ids[] = $row_course['course_id'];
    }

    // Close the course statement
    $stmt_course->close();

    // Fetch quizzes based on the course IDs
    foreach ($course_ids as $course_id) {
        $query_quizzes = "SELECT * FROM quizzes WHERE course_id = ?";
        $stmt_quizzes = $conn->prepare($query_quizzes);
        $stmt_quizzes->bind_param('i', $course_id);
        $stmt_quizzes->execute();
        $result_quizzes = $stmt_quizzes->get_result();

        // Check if quizzes are found for the course
        if ($result_quizzes->num_rows > 0) {
            while ($row_quiz = $result_quizzes->fetch_assoc()) {
                $quizzes[] = $row_quiz;
            }
        }

        // Close the quizzes statement
        $stmt_quizzes->close();
    }
} else {
    echo "No courses found for the student.";
}

// Check if the quiz ID is provided in the GET method
if (isset($_GET['quiz_id'])) {
    // Retrieve the quiz ID from the GET method
    $quiz_id = $_GET['quiz_id'];

    // Fetch the questions for the quiz
    $query_questions = "SELECT q.question_id, q.question_text, o.option_id, o.option_text
                        FROM questions q
                        INNER JOIN options o ON q.question_id = o.question_id
                        WHERE q.quiz_id = ?";
    $stmt_questions = $conn->prepare($query_questions);
    $stmt_questions->bind_param('i', $quiz_id);
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();

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

    $stmt_questions->close();
}

// Close the database connection
$conn->close();
?>
