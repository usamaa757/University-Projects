<?php
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection settings
$host = 'localhost';
$dbname = 'school';
$username = 'root';
$password = '';

// Create a connection to the database using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the teacher ID from session
$teacher_id = $_SESSION['teacher_id'];

// Retrieve class ID from the form
if (isset($_POST['class_id']) && isset($_POST['status'])) {
    $class_id = intval($_POST['class_id']);
    $status = $_POST['status'];

    // Loop through each student status and save the attendance
    foreach ($status as $student_id => $attendance_status) {
        $stmt = $conn->prepare('INSERT INTO attendance (teacher_id, course_id, class_id, date, student_id, status)
                                VALUES (?, ?, ?, CURDATE(), ?, ?)');
        $stmt->bind_param('iiisi', $teacher_id, $class_id, $student_id, $attendance_status);

        if ($stmt->execute()) {
            echo "Attendance marked successfully for student ID: $student_id.<br>";
        } else {
            echo "Error marking attendance for student ID: $student_id: " . $stmt->error . "<br>";
        }

        // Close statement
        $stmt->close();
    }
} else {
    echo "No class selected or no attendance data provided.";
}

// Close the database connection
$conn->close();
?>
