<?php
// create_notification.php
session_start();
require_once '../db_connection.php'; // Adjust the path to your DB connection file



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $title = $_POST['title'];
    $message = $_POST['message'];
    $notification_type = $_POST['notification_type'];
    $admin_id = $_SESSION['admin_id']; // Assuming admin_id is stored in session

    // Insert notification into the database
    $sql = "INSERT INTO notifications (title, message, notification_type, created_by)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $title, $message, $notification_type, $admin_id);

    if ($stmt->execute()) {
        echo "Notification created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Notification</title>
</head>

<body>

    <h2>Create Notification</h2>

    <form action="create_notification.php" method="POST">
        <label for="title">Notification Title:</label>
        <input type="text" name="title" required>
        <br><br>

        <label for="message">Message:</label>
        <textarea name="message" rows="4" required></textarea>
        <br><br>

        <label for="notification_type">Notification Type:</label>
        <select name="notification_type" required>
            <option value="event">Event</option>
            <option value="holiday">Holiday</option>
            <option value="closure">Emergency Closure</option>
        </select>
        <br><br>

        <button type="submit">Create Notification</button>
    </form>

</body>

</html>