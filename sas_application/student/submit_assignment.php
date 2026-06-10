<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

// Fetch student's class
$student_id = $_SESSION['user_id'];
$query = "SELECT class_id FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student_data = $result->fetch_assoc();
$student_class_id = $student_data['class_id'];

// Fetch assignments for the student's class
$query = "SELECT a.assignment_id, a.teacher_id, a.class_id, a.course_id, a.title, a.due_date, a.assignment_file,
                  c.course_name, cl.class_name
          FROM assignments a
          JOIN courses c ON a.course_id = c.course_id
          JOIN classes cl ON a.class_id = cl.class_id
          WHERE a.class_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_class_id);
$stmt->execute();
$result = $stmt->get_result();
$student_assignments = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Assignments</title>
    <link rel="stylesheet" href="../css/form.css">
    
</head>
<body>
    <h1>Student Assignments</h1>
    <ul>
    <?php foreach ($student_assignments as $assignment): ?>
        <li>
            <strong><?= $assignment['title'] ?></strong><br>
            <em>Course:</em> <?= $assignment['course_name'] ?><br>
            <em>Class:</em> <?= $assignment['class_name'] ?><br>
            <em>Due Date:</em> <?= $assignment['due_date'] ?><br>
            <a href="<?= $assignment['assignment_file'] ?>" download>Download Assignment</a>

        </li>
    <?php endforeach; ?>
    </ul>
</body>
</html>
