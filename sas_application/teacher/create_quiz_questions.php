<?php
session_start();
include '../other/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$quiz_id = $_GET['quiz_id'];

// Fetch the total number of questions for the given quiz
$query_total_questions = "SELECT COUNT(*) AS total_questions FROM questions WHERE quiz_id = ?";
$stmt_total_questions = $conn->prepare($query_total_questions);
$stmt_total_questions->bind_param("i", $quiz_id);
$stmt_total_questions->execute();
$result_total_questions = $stmt_total_questions->get_result();
$row_total_questions = $result_total_questions->fetch_assoc();
$total_questions = $row_total_questions['total_questions'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['question_text'], $_POST['answers'], $_POST['correct'])) {
        $question_text = $_POST['question_text'];

        // Insert question into the database
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $quiz_id, $question_text);
        $stmt->execute();
        $question_id = $stmt->insert_id;

        // Insert answers into the database
        foreach ($_POST['answers'] as $answer_key => $answer_text) {
            $is_correct = ($_POST['correct'] == $answer_key) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO options (question_id, answer_text, is_correct, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("isi", $question_id, $answer_text, $is_correct);
            $stmt->execute();
        }

        // Redirect with a success message
        header("Location: create_quiz_questions.php?quiz_id=$quiz_id&msg=Question+added+successfully");
        exit();
    } else {
        echo "Required fields are missing.";
    }
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : ''; // Fetch the message if it exists
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question</title>
</head>
<body>
    <h1>Add Question to Quiz</h1>
    <p>Total Questions for Quiz: <?= htmlspecialchars($total_questions) ?></p>
    <form action="create_quiz_questions.php?quiz_id=<?= $quiz_id ?>" method="post">
    <?php if ($msg): ?>
        <p style="color: green;"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
        <label for="question_text">Question:</label>
        <input type="text" name="question_text" id="question_text" required>
        <br>
        <label>Answers:</label>
        <div>
            <input type="text" name="answers[answer1]" placeholder="Answer 1" required>
            <label><input type="radio" name="correct" value="answer1" required> Correct</label>
        </div>
        <div>
            <input type="text" name="answers[answer2]" placeholder="Answer 2" required>
            <label><input type="radio" name="correct" value="answer2"> Correct</label>
        </div>
        <div>
            <input type="text" name="answers[answer3]" placeholder="Answer 3">
            <label><input type="radio" name="correct" value="answer3"> Correct</label >
        </div>
        <div>
            <input type="text" name="answers[answer4]" placeholder="Answer 4">
            <label><input type="radio" name="correct" value="answer4"> Correct</label>
        </div>
        <button type="submit">Add Question</button>
    </form>
</body>
</html>
