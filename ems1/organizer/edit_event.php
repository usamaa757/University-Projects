<?php
include 'header.php';
include '../db.php';

$message = "";
$message_type = "";

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    if (!$event) {
        $message = "Event not found!";
        $message_type = "error";
    }
} else {
    $message = "Event ID is required";
    $message_type = "error";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];

    $update_stmt = $conn->prepare("UPDATE events SET event_name = ?, location = ?, event_description = ?, event_date = ? WHERE event_id = ?");
    $update_stmt->bind_param("ssssi", $event_name, $location, $event_description, $event_date, $event_id);

    if ($update_stmt->execute()) {
        $message = "Event updated successfully!";
        $message_type = "success";
        // Refresh event details after update
        $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
    } else {
        $message = "Error updating event: " . $update_stmt->error;
        $message_type = "error";
    }
    $update_stmt->close();
}
if (isset($_GET['message']) && isset($_GET['message_type'])) {
    $message = $_GET['message'];
    $message_type = $_GET['message_type'];
} else {
    $message = ''; // Default empty message if not set
    $message_type = ''; // Default empty message type
}
?>
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .form-container {
        max-width: 500px;
        margin: 50px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    h3 {
        text-align: center;
        margin-bottom: 25px;
    }

    .message {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="date"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
    }

    button[type="submit"] {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        display: block;
        margin: 0 auto;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>

    <div class="form-container">
        <h3>Edit Event</h3>

        <?php if (!empty($message)): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($event)): ?>
        <form method="POST">
            <label for="event_name">Event Name</label>
            <input type="text" name="event_name" id="event_name" value="<?= htmlspecialchars($event['event_name']) ?>"
                required>

            <label for="location">Event Location</label>
            <input type="text" name="location" id="location" value="<?= htmlspecialchars($event['location']) ?>"
                required>

            <label for="event_description">Event Description</label>
            <textarea name="event_description" id="event_description" rows="4"
                required><?= htmlspecialchars($event['event_description']) ?></textarea>

            <label for="event_date">Event Date</label>
            <input type="date" name="event_date" id="event_date" value="<?= $event['event_date'] ?>" required>

            <button type="submit">Update Event</button>
        </form>
        <?php endif; ?>
    </div>

</body>

</html>