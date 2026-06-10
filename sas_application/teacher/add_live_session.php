<?php
session_start(); // Start session

// Check if teacher is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or display an error message
    header("Location: login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sas";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Retrieve teacher ID from session
$teacher_id = $_SESSION['user_id'];

// Retrieve class and course IDs from the form submission
$class_id = $_POST['class_id'];
$course_id = $_POST['course_id'];

// Perform authorization check to ensure teacher can add session
$authorized = false; // Assume not authorized by default

// Check if teacher is authorized for the specified class
$sql_class = "SELECT * FROM teachers WHERE teacher_id = $teacher_id AND class_id = $class_id";
$result_class = $mysqli->query($sql_class);

// Check if teacher is authorized for the specified course
$sql_course = "SELECT * FROM teachers WHERE teacher_id = $teacher_id AND course_id = $course_id";
$result_course = $mysqli->query($sql_course);

if ($result_class->num_rows > 0 || $result_course->num_rows > 0) {
    // Teacher is authorized if they are associated with the specified class or course
    $authorized = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($authorized) {
        // If authorized, proceed with adding the lecture session

        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $duration = $_POST['duration'];
        $session_link = $_POST['session_link'];

        $stmt = $mysqli->prepare('INSERT INTO live_session (title, description, date, duration, teacher_id, session_link, class_id, course_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssiiii', $title, $description, $date, $duration, $teacher_id, $session_link, $class_id, $course_id);
        $stmt->execute();

        if ($stmt->error) {
            echo "Failed to add lecture session: " . $stmt->error;
        } else {
            echo "Lecture session added successfully!";
        }

        $stmt->close();
    } else {
        echo "You are not authorized to add a lecture session for this class/course.";
    }
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Lecture Session</title>
</head>
<body>
    <h1>Add Lecture Session</h1>
    <form action="add_live_session.php" method="POST">
        <label>Title:</label>
        <input type="text" name="title" required><br>

        <label>Description:</label>
        <textarea name="description" required></textarea><br>

        <label>Date:</label>
        <input type="date" name="date" required><br>

        <label>Duration (minutes):</label>
        <input type="number" name="duration" required><br>

        <!-- Add class_id and course_id fields in the form -->
        <label>Class ID:</label>
        <input type="number" name="class_id" required><br>

        <label>Course ID:</label>
        <input type="number" name="course_id" required><br>

        <!-- You can hide the teacher ID field to prevent users from tampering with it -->
        <!-- <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>"> -->

        <label>Session Link:</label>
        <input type="text" name="session_link" required><br>

        <input type="submit" value="Add Session">
    </form>
</body>
</html>
