<?php
// mark_attendance.php
session_start();
require_once 'db_connection.php'; // Include your DB connection

// Fetch children list
$childrenResult = $conn->query("SELECT child_id, name FROM children");

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_id = $_POST['child_id'];
    $action = $_POST['action'];
    $today = date('Y-m-d');
    $currentTime = date('H:i:s');

    // Check if attendance already exists for today
    $check = $conn->prepare("SELECT * FROM attendance WHERE child_id = ? AND date = ?");
    $check->bind_param("is", $child_id, $today);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $row = $result->fetch_assoc();
        if ($action == 'check_in') {
            $message = "Already checked in.";
        } elseif ($action == 'check_out') {
            $update = $conn->prepare("UPDATE attendance SET check_out_time = ? WHERE child_id = ? AND date = ?");
            $update->bind_param("sis", $currentTime, $child_id, $today);
            if ($update->execute()) {
                $message = "Checked out successfully!";
            }
        }
    } else {
        // Insert new check-in
        if ($action == 'check_in') {
            $insert = $conn->prepare("INSERT INTO attendance (child_id, date, check_in_time) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $child_id, $today, $currentTime);
            if ($insert->execute()) {
                $message = "Checked in successfully!";
            }
        } else {
            $message = "You need to check in first!";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Mark Attendance</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 30px;
        background-color: #f5f5f5;
    }

    h2 {
        text-align: center;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 0 auto;
    }

    select,
    button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
    }

    .msg {
        text-align: center;
        color: green;
        font-weight: bold;
    }
    </style>
</head>

<body>

    <h2>Mark Attendance</h2>

    <form method="POST" action="">
        <label for="child_id">Select Child:</label>
        <select name="child_id" id="child_id" required>
            <option value="">-- Choose Child --</option>
            <?php while ($child = $childrenResult->fetch_assoc()): ?>
            <option value="<?= $child['child_id'] ?>"><?= htmlspecialchars($child['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="action">Action:</label>
        <select name="action" id="action" required>
            <option value="">-- Select Action --</option>
            <option value="check_in">Check In</option>
            <option value="check_out">Check Out</option>
        </select>

        <button type="submit">Submit</button>

        <?php if (!empty($message)): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </form>

</body>

</html>