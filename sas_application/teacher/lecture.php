<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the teacher is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$teacher_id = $_SESSION['user_id'];

// Fetch classes and courses for the logged-in teacher
$query = "SELECT tcc.class_id, tcc.course_id, c.course_name, cl.class_name
          FROM teacher_class_course tcc
          JOIN courses c ON tcc.course_id = c.course_id
          JOIN classes cl ON tcc.class_id = cl.class_id
          WHERE tcc.teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher_courses = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure class_course and title are set
    if (isset($_POST['class_course'], $_POST['title'], $_POST['video_url'])) {
        // Extract class_id and course_id from the combined value
        list($class_id, $course_id) = explode('-', $_POST['class_course']);
        $title = $_POST['title'];
        $video_url = $_POST['video_url'];

        // Save lecture details in the database
        $stmt = $conn->prepare("INSERT INTO lectures (teacher_id, class_id, course_id, title, video_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $teacher_id, $class_id, $course_id, $title, $video_url);
        if ($stmt->execute()) {
            echo "Lecture uploaded successfully.";
        } else {
            echo "Database error: " . $stmt->error;
        }
    } else {
        echo "Required fields are missing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Lecture</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <header>
    <h1>Upload Lecture</h1>
    </header>
    <form action="lecture.php" method="post">
        <label for="class_course">Class and Course:</label>
        <select name="class_course" id="class_course" required>
            <option value="">Select Class and Course</option>
            <?php foreach ($teacher_courses as $course): ?>
                <option value="<?= $course['class_id'] . '-' . $course['course_id'] ?>">
                    <?= $course['class_name'] ?> - <?= $course['course_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <br>
        <label for="video_url">Video URL:</label>
        <input type="url" name="video_url" id="video_url" required>
        <br>
        <br>
        <button type="submit">Upload Lecture</button>
    </form>
</body>
</html>
