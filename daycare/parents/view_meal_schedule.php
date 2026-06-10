<?php
// view_meal_schedule.php
require_once '../db_connection.php'; // Adjust the path to your DB connection file

// Fetch meal schedule from the database
$sql = "SELECT ms.day_of_week, ms.breakfast, ms.lunch, ms.snacks, s.name AS recorded_by
        FROM meal_schedule ms
        JOIN staff s ON ms.staff_id = s.staff_id
        ORDER BY FIELD(ms.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Meal Schedule</title>
</head>

<body>

    <h2>Weekly Meal Schedule</h2>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Day of the Week</th>
                <th>Breakfast</th>
                <th>Lunch</th>
                <th>Snacks</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                <td><?= htmlspecialchars($row['breakfast']) ?></td>
                <td><?= htmlspecialchars($row['lunch']) ?></td>
                <td><?= htmlspecialchars($row['snacks']) ?></td>
                <td><?= htmlspecialchars($row['recorded_by']) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="5">No meal schedule found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>