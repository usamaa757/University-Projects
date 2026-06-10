<?php
session_start();
include '../other/db_connection.php'; // Include database connection

// Check if the teacher is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$teacher_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure title, start_date, and end_date are set
    if (isset($_POST['title'], $_POST['start_date'], $_POST['end_date'])) {
        $title = $_POST['title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Insert quiz into the database
        $stmt = $conn->prepare("INSERT INTO quizzes (title, start_date, end_date, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $title, $start_date, $end_date);
        if ($stmt->execute()) {
            header('Location: create_quiz.php?msg=Quiz created successfully');
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }
    } else {
        echo "Required fields are missing.";
    }
}

// Fetch all quizzes
$query = "SELECT * FROM quizzes ORDER BY created_at DESC";
$result = $conn->query($query);
$quizzes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz</title>
</head>
<body>
    <h1>Create Quiz</h1>
    <form action="create_quiz.php" method="post">
        <label for="title">Quiz Title:</label>
        <input type="text" name="title" id="title" required>
        <br>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" required>
        <br>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" required>
        <br>
        <button type="submit">Create Quiz</button>
    </form>

    <h2>Existing Quizzes</h2>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Title</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($quizzes as $quiz): ?>
            <tr>
                <td><?= htmlspecialchars($quiz['title']) ?></td>
                <td><?= htmlspecialchars($quiz['start_date']) ?></td>
                <td><?= htmlspecialchars($quiz['end_date']) ?></td>
                <td>
                    <a href="create_quiz_questions.php?quiz_id=<?= $quiz['quiz_id'] ?>">Add Questions</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
