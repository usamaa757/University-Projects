<?php
session_start();
include '../include_files/db_connection.php';

// Check if the form is submitted and the quiz ID is provided
if ($_SERVER["REQUEST_METHOD"] === "POST") {


    // Retrieve selected answers and other quiz details
    $answers = $_POST['answers'];
    $student_id = $_SESSION['student_id'];
    $quiz_id = $_POST['quiz_id'];
    $status = 'submitted';
    
    // Insert student ID and quiz ID into student_quiz table
    $query_insert_student_quiz = "INSERT INTO student_quiz (student_id, quiz_id, status) VALUES (?, ?, ?)";
    $statement_insert_student_quiz = $conn->prepare($query_insert_student_quiz);
    $statement_insert_student_quiz->bind_param('iis', $student_id, $quiz_id, $status);
    $statement_insert_student_quiz->execute();
    $statement_insert_student_quiz->close();

    // Retrieve the auto-generated student_quiz_id
    $student_quiz_id = $conn->insert_id;

    // Initialize marks
    $marks = 0;

    // Loop through submitted answers
    foreach ($answers as $question_id => $selected_option_id) {
        // Insert the response into the student_quiz_answer table
        $query_insert_response = "INSERT INTO student_quiz_answers (student_quiz_id, question_id, selected_option_id) VALUES (?, ?, ?)";
        $statement_insert_response = $conn->prepare($query_insert_response);
        $statement_insert_response->bind_param('iii', $student_quiz_id, $question_id, $selected_option_id);
        $statement_insert_response->execute();
        $statement_insert_response->close();

        // Check if the selected option is correct
        $query_check_answer = "SELECT COUNT(*) as count FROM options WHERE question_id = ? AND option_id = ? AND is_correct = 1";
        $statement_check_answer = $conn->prepare($query_check_answer);
        $statement_check_answer->bind_param('ii', $question_id, $selected_option_id);
        $statement_check_answer->execute();
        $result_check_answer = $statement_check_answer->get_result();
        $row_check_answer = $result_check_answer->fetch_assoc();

        // If the selected option is correct, increment the marks
        if ($row_check_answer['count'] > 0) {
            $marks++;
        }
    }

    // Insert the marks into the database
    $query_insert_marks = "UPDATE student_quiz SET marks = ? WHERE student_quiz_id = ?";
    $statement_insert_marks = $conn->prepare($query_insert_marks);
    $statement_insert_marks->bind_param('ii', $marks, $student_quiz_id);
    $statement_insert_marks->execute();
    $statement_insert_marks->close();

    echo "<script>
    alert('Quiz submitted successfully!');
    window.location.href = 'quiz_list.php';
</script>";
    header("Location: quiz_list.php");
    exit();
}
