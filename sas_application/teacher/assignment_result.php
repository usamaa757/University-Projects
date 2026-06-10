<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$student_id = $_SESSION['user_id'];

// Fetch quiz results for the student
$quiz_query = "SELECT q.title AS quiz_title, qr.score 
               FROM quiz_results qr
               JOIN quizzes q ON qr.quiz_id = q.quiz_id
               WHERE qr.student_id = ?";
$stmt = $conn->prepare($quiz_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$quiz_result = $stmt->get_result();
$quiz_results = $quiz_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch assignment results for the student
$assignment_query = "SELECT a.title AS assignment_title, ar.score 
                     FROM assignment_results ar
                     JOIN assignments a ON ar.assignment_id = a.assignment_id
                     WHERE ar.student_id = ?";
$stmt = $conn->prepare($assignment_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$assignment_result = $stmt->get_result();
$assignment_results = $assignment_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Results</title>
</head>
<body>
    <h1>Quiz Results</h1>
    <ul>
        <?php foreach ($quiz_results as $result): ?>
            <li><strong><?= htmlspecialchars($result['quiz_title']) ?>:</strong> <?= htmlspecialchars($result['score']) ?></li>
        <?php endforeach; ?>
    </ul>

    <h1>Assignment Results</h1>
    <ul>
        <?php foreach ($assignment_results as $result): ?>
            <li><strong><?= htmlspecialchars($result['assignment_title']) ?>:</strong> <?= htmlspecialchars($result['score']) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
