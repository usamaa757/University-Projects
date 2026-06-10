<?php
session_start(); // Start the session
include '../db_connection.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the update questions button was clicked
    if (isset($_POST['update_questions'])) {
        $exam_id = $_POST['exam_id'];
        $exam_type = $_POST['exam_type'];
        $subject_id = $_POST['subject_id'];
        $questions = $_POST['questions'];
        $options = $_POST['options']; // Option text updates
        $correct_options = $_POST['correct_option']; // Correct option IDs
        if ($exam_type === "Mid") {
            $table_name = "mid_exams";
            $questions_table = "mid_exam_questions";
            $options_table = "mid_exam_options";
        } elseif ($exam_type === "Final") {
            $table_name = "final_exams";
            $questions_table = "final_exam_questions";
            $options_table = "final_exam_options";
        } else {
            die("Invalid exam type.");
        }
        // Update each question
        foreach ($questions as $question_id => $question_text) {
            // Update question text
            $update_sql = "UPDATE $questions_table SET question_text = ? WHERE question_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $question_text, $question_id);
            $update_stmt->execute();
            $update_stmt->close();
        }

        // Update options and correct option
        foreach ($options as $option_id => $option_text) {
            // Update option text
            $update_option_text_sql = "UPDATE $options_table SET option_text = ? WHERE option_id = ?";
            $update_option_text_stmt = $conn->prepare($update_option_text_sql);
            $update_option_text_stmt->bind_param("si", $option_text, $option_id);
            $update_option_text_stmt->execute();
            $update_option_text_stmt->close();
        }

        // Update correct options
        foreach ($correct_options as $question_id => $correct_option_id) {
            // Set all options for the question as incorrect
            $update_option_sql = "UPDATE $options_table SET is_correct = 0 WHERE question_id = ?";
            $update_option_stmt = $conn->prepare($update_option_sql);
            $update_option_stmt->bind_param("i", $question_id);
            $update_option_stmt->execute();
            $update_option_stmt->close();

            // Set the correct option
            $set_correct_sql = "UPDATE $options_table SET is_correct = 1 WHERE option_id = ?";
            $set_correct_stmt = $conn->prepare($set_correct_sql);
            $set_correct_stmt->bind_param("i", $correct_option_id);
            $set_correct_stmt->execute();
            $set_correct_stmt->close();
        }

        // Set success message in the session
        $_SESSION['success'] = "Questions and options updated successfully!";
        header("Location: edit_questions.php?exam_id=$exam_id&exam_type=$exam_type&subject_id=$subject_id"); // Redirect to the same page to display the message
        exit();
    }

    // Check if the delete question button was clicked
    if (isset($_GET['delete_question'])) {
        $question_id = $_GET['delete_question'];

        // First, delete all options for the question
        $delete_options_sql = "DELETE FROM $options_table WHERE question_id = ?";
        $delete_options_stmt = $conn->prepare($delete_options_sql);
        $delete_options_stmt->bind_param("i", $question_id);
        $delete_options_stmt->execute();
        $delete_options_stmt->close();

        // Now delete the question itself
        $delete_question_sql = "DELETE FROM $questions_table WHERE question_id = ? AND exam_id = ?";
        $delete_question_stmt = $conn->prepare($delete_question_sql);
        $delete_question_stmt->bind_param("ii", $question_id, $exam_id);
        $delete_question_stmt->execute();
        $delete_question_stmt->close();

        // Set success message in the session
        $_SESSION['error'] = "Question deleted successfully!";
        header("Location: edit_questions.php?exam_id=$exam_id&exam_type=$exam_type&subject_id=$subject_id"); // Redirect to the same page to display the message
        exit();
    }
}

$conn->close(); // Close the database connection