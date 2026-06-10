<?php
// children_attendance.php
session_start();
require_once '../db_connection.php'; // include your DB connection file

// Optional: Only allow access if staff/admin is logged in
// if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
//     header("Location: login.php");
//     exit();
// }

// Fetch children and their attendance
$sql = "SELECT 
            c.name AS child_name, 
            p.name AS parent_name, 
            a.date, 
            a.check_in_time, 
            a.check_out_time
        FROM attendance a
        JOIN children c ON a.child_id = c.child_id
        JOIN parents p ON c.parent_id = p.parent_id
        ORDER BY a.date DESC, c.name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Children Attendance Records</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
    }

    h2 {
        text-align: center;
    }

    table {
        border-collapse: collapse;
        width: 90%;
        margin: 0 auto;
    }

    th,
    td {
        border: 1px solid #999;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #eee;
    }
    </style>
</head>

<body>

    <h2>Children Attendance Records</h2>

    <table>
        <thead>
            <tr>
                <th>Child Name</th>
                <th>Parent Name</th>
                <th>Date</th>
                <th>Check-In Time</th>
                <th>Check-Out Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['child_name']) ?></td>
                <td><?= htmlspecialchars($row['parent_name']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= $row['check_in_time'] ?? '-' ?></td>
                <td><?= $row['check_out_time'] ?? '-' ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="4">No attendance records found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>