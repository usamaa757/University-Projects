<?php
include 'header.php';
include '../db.php';

// Check if the user is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_description = $_POST['event_description'];
    $organizer_id = $_SESSION['user_id'];  // Organizer ID from session
    $location = $_POST['location'];

    // Insert event into the database
    $stmt = $conn->prepare("INSERT INTO events (event_name, location, event_date, event_description, organizer_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $event_name, $location, $event_date, $event_description, $organizer_id);

    if ($stmt->execute()) {
        $message = "Event created successfully!";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "error";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
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
        <h3>Create Event</h3>

        <?php if (!empty($message)): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <form action="create_event.php" method="POST">
            <label for="event_name">Event Name</label>
            <input type="text" name="event_name" id="event_name" required placeholder="Enter event name">

            <label for="location">Event Location</label>
            <input type="text" name="location" id="location" required placeholder="Enter event location">

            <label for="event_date">Event Date</label>
            <input type="date" name="event_date" id="event_date" required>

            <label for="event_description">Event Description</label>
            <textarea name="event_description" id="event_description" required
                placeholder="Enter event description"></textarea>

            <button type="submit">Create Event</button>
        </form>
    </div>

</body>

</html>