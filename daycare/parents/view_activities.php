<?php
// view_daily_activities.php
require_once '../db_connection.php'; // adjust path if needed

// Fetch daily activities with child and staff info
$sql = "SELECT da.activity_date, c.name AS child_name, da.playtime, da.learning, da.meals, da.naps, s.name AS recorded_by
        FROM daily_activities da
        JOIN children c ON da.child_id = c.child_id
        JOIN staff s ON da.recorded_by = s.staff_id
        ORDER BY da.activity_date DESC, c.name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Daily Activities Log</title>
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
        width: 95%;
        margin: 0 auto;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #f2f2f2;
    }
    </style>
</head>

<body>
    <h2>Daily Activities Log</h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Child Name</th>
                <th>Playtime</th>
                <th>Learning</th>
                <th>Meals</th>
                <th>Naps</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['activity_date']) ?></td>
                <td><?= htmlspecialchars($row['child_name']) ?></td>
                <td><?= htmlspecialchars($row['playtime']) ?></td>
                <td><?= htmlspecialchars($row['learning']) ?></td>
                <td><?= htmlspecialchars($row['meals']) ?></td>
                <td><?= htmlspecialchars($row['naps']) ?></td>
                <td><?= htmlspecialchars($row['recorded_by']) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7">No activity records found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>