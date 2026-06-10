<?php
include '../other/db_connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Fetch attendance records within the date range for the student
    $query = "SELECT status, COUNT(*) AS count
              FROM attendance
              WHERE student_id = ? AND attendance_date BETWEEN ? AND ?
              GROUP BY status";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $student_id, $from_date, $to_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize counts
    $total_present = 0;
    $total_absent = 0;

    // Calculate totals
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] === 'Present') {
            $total_present = $row['count'];
        } elseif ($row['status'] === 'Absent') {
            $total_absent = $row['count'];
        }
    }

    // Calculate total days and attendance percentage
    $total_days = $total_present + $total_absent;
    $attendance_percentage = $total_days > 0 ? ($total_present / $total_days) * 100 : 0;

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
</head>

<body>
    <h2>Attendance Report for Student ID: <?= htmlspecialchars($student_id) ?></h2>
    <p>Date Range: <?= htmlspecialchars($from_date) ?> to <?= htmlspecialchars($to_date) ?></p>

    <table border="1">
        <tr>
            <th>Total Present Days</th>
            <td><?= $total_present ?></td>
        </tr>
        <tr>
            <th>Total Absent Days</th>
            <td><?= $total_absent ?></td>
        </tr>
        <tr>
            <th>Total Days</th>
            <td><?= $total_days ?></td>
        </tr>
        <tr>
            <th>Attendance Percentage</th>
            <td><?= number_format($attendance_percentage, 2) ?>%</td>
        </tr>
    </table>

    <br>
    <a href="index.php">Back to Form</a>
</body>

</html>
