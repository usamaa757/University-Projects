<?php
// create_meal_schedule.php
session_start();
require_once '../db_connection.php'; // Adjust the path to your DB connection file


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $day_of_week = $_POST['day_of_week'];
    $breakfast = $_POST['breakfast'];
    $lunch = $_POST['lunch'];
    $snacks = $_POST['snacks'];
    $staff_id = 1; // Assuming staff_id is stored in session

    // Insert into meal_schedule table
    $sql = "INSERT INTO meal_schedule (day_of_week, breakfast, lunch, snacks, staff_id)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $day_of_week, $breakfast, $lunch, $snacks, $staff_id);

    if ($stmt->execute()) {
        echo "Meal schedule added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Meal Schedule</title>
</head>

<body>

    <h2>Create Daily/Weekly Meal Schedule</h2>

    <form action="create_meal_schedule.php" method="POST">
        <label for="day_of_week">Day of the Week:</label>
        <select name="day_of_week" required>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
            <option value="Sunday">Sunday</option>
        </select>
        <br><br>

        <label for="breakfast">Breakfast:</label>
        <input type="text" name="breakfast" required>
        <br><br>

        <label for="lunch">Lunch:</label>
        <input type="text" name="lunch" required>
        <br><br>

        <label for="snacks">Snacks:</label>
        <input type="text" name="snacks" required>
        <br><br>

        <button type="submit">Save Meal Schedule</button>
    </form>

</body>

</html>