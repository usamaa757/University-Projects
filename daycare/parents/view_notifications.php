<?php
// view_notifications.php
require_once '../db_connection.php'; // Adjust the path to your DB connection file

// Fetch all notifications from the database
$sql = "SELECT n.title, n.message, n.notification_date, n.notification_type, a.name AS created_by
        FROM notifications n
        JOIN staff a ON n.created_by = a.staff_id
        ORDER BY n.notification_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Notifications</title>
</head>

<body>

    <h2>Notifications</h2>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Title</th>
                <th>Message</th>
                <th>Notification Type</th>
                <th>Date</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['notification_type'])) ?></td>
                <td><?= $row['notification_date'] ?></td>
                <td><?= htmlspecialchars($row['created_by']) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="5">No notifications found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>