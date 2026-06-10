<?php
// Include the database connection file
include '../other/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_data = $_POST['attendance'];
    $date = date('Y-m-d'); // Current date

    foreach ($attendance_data as $teacher_id => $status) {
        // Prepare the SQL statement to insert attendance
        $stmt = $conn->prepare("INSERT INTO teacher_attendance (teacher_id, date, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $teacher_id, $date, $status);
        $stmt->execute();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to a confirmation page or the same page with a success message
    header("Location: teacher_attendance.php?message=Attendance marked successfully!");
    exit;
}
?>
