<?php
require_once '../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id = $_POST['child_id'];
    $activity_date = $_POST['activity_date'];
    $playtime = $_POST['playtime'];
    $learning = $_POST['learning'];
    $meals = $_POST['meals'];
    $naps = $_POST['naps'];
    $staff_id = 1;

    $stmt = $conn->prepare("INSERT INTO daily_activities 
        (child_id, activity_date, playtime, learning, meals, naps, recorded_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $child_id, $activity_date, $playtime, $learning, $meals, $naps, $staff_id);

    if ($stmt->execute()) {
        echo "Activity log submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}


// Fetch children for dropdown
$children = $conn->query("SELECT child_id, name FROM children");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Log Daily Activities</title>
    <style>
    body {
        font-family: Arial;
        padding: 20px;
    }

    form {
        max-width: 600px;
        margin: auto;
    }

    label,
    textarea,
    input,
    select {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <h2>Log Child Daily Activities</h2>

    <form method="POST">
        <label for="child_id">Select Child:</label>
        <select name="child_id" required>
            <option value="">-- Select --</option>
            <?php while ($row = $children->fetch_assoc()): ?>
            <option value="<?= $row['child_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="activity_date">Activity Date:</label>
        <input type="date" name="activity_date" required>

        <label>Playtime:</label>
        <textarea name="playtime" rows="2"></textarea>

        <label>Learning Session:</label>
        <textarea name="learning" rows="2"></textarea>

        <label>Meals:</label>
        <textarea name="meals" rows="2"></textarea>

        <label>Naps:</label>
        <textarea name="naps" rows="2"></textarea>

        <input type="submit" value="Submit Activity Log">
    </form>
</body>

</html>