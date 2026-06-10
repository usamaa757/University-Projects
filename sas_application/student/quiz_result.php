<?php
// Include database connection
include 'db.php';

// Assume user role and ID are available through session or some other means
$user_role = 'student'; // Change to 'teacher' for teachers
$user_id = 1; // Replace with actual user ID

if ($user_role === 'teacher') {
    // For teachers, show all quiz results for all students
    $results = $conn->query("SELECT student_id, quiz_id, score FROM student_quiz");
    echo "<h2>All Quiz Results</h2>";
    echo "<table>";
    echo "<tr><th>Student ID</th><th>Quiz ID</th><th>Score</th></tr>";

    while ($result = $results->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$result['student_id']}</td>";
        echo "<td>{$result['quiz_id']}</td>";
        echo "<td>{$result['score']}%</td>";
        echo "</tr>";
    }

    echo "</table>";
} else if ($user_role === 'student') {
    // For students, show their own quiz results
    $results = $conn->query("SELECT quiz_id, score FROM student_quiz WHERE student_id = $user_id");
    echo "<h2>Your Quiz Results</h2>";
    echo "<table>";
    echo "<tr><th>Quiz ID</th><th>Score</th></tr>";

    while ($result = $results->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$result['quiz_id']}</td>";
        echo "<td>{$result['score']}%</td>";
        echo "</tr>";
    }

    echo "</table>";
}
?>
