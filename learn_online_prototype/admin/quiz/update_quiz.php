<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$quiz_details = [];
$questions = [];

if ($quiz_id > 0) {
    // Fetch quiz details
    $quiz_stmt = $conn->prepare("SELECT start_date, end_date FROM quizzes WHERE quiz_id = ?");
    $quiz_stmt->bind_param("i", $quiz_id);
    $quiz_stmt->execute();
    $quiz_stmt->bind_result($start_date, $end_date);
    while ($quiz_stmt->fetch()) {
        $quiz_details[] = [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }
    $quiz_stmt->close();

    // Fetch questions and options
    $query = "
        SELECT q.question_id, q.question_text, o.option_id, o.option_text, o.is_correct
        FROM questions q
        LEFT JOIN options o ON q.question_id = o.question_id
        WHERE q.quiz_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $question_id = $row['question_id'];
        $question_text = $row['question_text'];
        $option_id = $row['option_id'];
        $option_text = $row['option_text'];
        $is_correct = $row['is_correct'];

        if (!isset($questions[$question_id])) {
            $questions[$question_id] = [
                'question_text' => $question_text,
                'options' => []
            ];
        }

        $questions[$question_id]['options'][] = [
            'option_id' => $option_id,
            'option_text' => $option_text,
            'is_correct' => $is_correct
        ];
    }

    $stmt->close();
} else {
    $errorMsg = "Invalid quiz ID.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['quiz_id'], $_POST['course_id'], $_POST['start_date'], $_POST['end_date'])) {
        $quiz_id = $_POST['quiz_id'];
        $course_id = $_POST['course_id'];
        $start_date = $_POST['start_date'][0];
        $end_date = $_POST['end_date'][0];

        // Update quiz dates
        $update_stmt = $conn->prepare("UPDATE quizzes SET start_date = ?, end_date = ? WHERE quiz_id = ?");
        $update_stmt->bind_param("ssi", $start_date, $end_date, $quiz_id);

        if ($update_stmt->execute()) {
            $resultMsg = "Quiz updated successfully.";
        } else {
            $errorMsg = "Error updating quiz: " . $conn->error;
        }

        $update_stmt->close();

        // Update questions and options
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $question_id => $question) {
                // Update question text
                $question_text = $question['question_text'];
                $update_question_stmt = $conn->prepare("UPDATE questions SET question_text = ? WHERE question_id = ?");
                $update_question_stmt->bind_param("si", $question_text, $question_id);
                $update_question_stmt->execute();
                $update_question_stmt->close();

                // Update options
                if (isset($question['options'])) {
                    $correct_option = isset($question['correct_option']) ? intval($question['correct_option']) : 0;
                    foreach ($question['options'] as $option_id => $option) {
                        $option_text = $option['option_text'];
                        $is_correct = ($option_id == $correct_option) ? 1 : 0;
                        $update_option_stmt = $conn->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE option_id = ?");
                        $update_option_stmt->bind_param("sii", $option_text, $is_correct, $option_id);
                        $update_option_stmt->execute();
                        $update_option_stmt->close();
                    }
                }
            }
        }

        header("Location: edit_quiz.php?quiz_id=$quiz_id &result=" . urlencode($resultMsg));
        exit();
    } else {
        $errorMsg = "Missing Quiz ID, course ID or dates.";
        header("Location: edit_quiz.php?quiz_id = $quiz_id &error=" . urlencode($errorMsg));
        exit();
    }
}

// Close connection
$conn->close();
?>