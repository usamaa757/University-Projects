<?php
include 'header.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $attendee_id = $_SESSION['user_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO rsvps (attendee_id, event_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");
    $stmt->bind_param("iiss", $attendee_id, $event_id, $status, $status);

    if ($stmt->execute()) {
        $message = "RSVP successful!";
        $msg_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $msg_type = "error";
    }
}

// Fetch events
$stmt = $conn->prepare("SELECT * FROM events ORDER BY event_date ASC");
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);

// Fetch current RSVP status for the logged-in user
$rsvp_status = [];
$rsvp_stmt = $conn->prepare("SELECT event_id, status FROM rsvps WHERE attendee_id = ?");
$rsvp_stmt->bind_param("i", $_SESSION['user_id']);
$rsvp_stmt->execute();
$rsvp_result = $rsvp_stmt->get_result();
while ($row = $rsvp_result->fetch_assoc()) {
    $rsvp_status[$row['event_id']] = $row['status'];
}

$stmt->close();
$rsvp_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Events</title>
    <style>
    /* Styles remain unchanged */
    .event-container {
        width: 90%;
        margin: 40px auto;
        padding: 20px;
    }

    .search-form {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-form input[type="text"],
    .search-form input[type="date"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 45%;
    }

    .search-form button {
        background-color: #3498db;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .search-form button:hover {
        background-color: #2980b9;
    }

    .event-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .event-card h3 {
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .event-card p {
        color: #7f8c8d;
        margin-bottom: 15px;
    }

    .event-card .details {
        margin-bottom: 20px;
    }

    .rsvp-form {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rsvp-form label {
        margin-right: 15px;
    }

    .rsvp-form input[type="radio"] {
        margin-right: 5px;
    }

    .rsvp-form button {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .rsvp-form button:hover {
        background-color: #218838;
    }

    .no-events {
        text-align: center;
        color: #7f8c8d;
        font-size: 18px;
    }

    .message-container {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }

    .message-container.success {
        background-color: #d4edda;
        color: #155724;
    }

    .message-container.error {
        background-color: #f8d7da;
        color: #721c24;
    }
    </style>
</head>

<body>

    <div class="event-container">
        <!-- Display Success or Error Message -->
        <?php if (isset($message)): ?>
        <div class="message-container <?= $msg_type; ?>">
            <?= htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form method="GET" class="search-form" action="search_event.php">
            <input type="text" name="keyword" placeholder="Search by event name or description"
                value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <input type="date" name="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (empty($events)): ?>
        <p class="no-events">No events found.</p>
        <?php else: ?>

        <?php foreach ($events as $event): ?>
        <div class="event-card">
            <h3><?= htmlspecialchars($event['event_name']) ?></h3>
            <p><?= htmlspecialchars($event['event_description']) ?></p>
            <div class="details">
                <p><strong>Date:</strong> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
            </div>
            <?php
                    // Get today's date in 'Y-m-d' format
                    $current_date = date('Y-m-d');


                    // Compare the dates
                    if ($current_date > $event['event_date']) {
                        echo '<div class="message-container">Event has already passed.</div>';
                    } else {
                    ?>
            <form action="event_list.php" method="post" class="rsvp-form">
                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

                <!-- Radio Buttons for RSVP Status -->
                <label>
                    <input type="radio" name="status" value="attend"
                        <?= (isset($rsvp_status[$event['event_id']]) && $rsvp_status[$event['event_id']] == 'attend') ? 'checked' : ''; ?>
                        required> Attend
                </label>
                <label>
                    <input type="radio" name="status" value="maybe"
                        <?= (isset($rsvp_status[$event['event_id']]) && $rsvp_status[$event['event_id']] == 'maybe') ? 'checked' : ''; ?>
                        required> Maybe
                </label>
                <label>
                    <input type="radio" name="status" value="decline"
                        <?= (isset($rsvp_status[$event['event_id']]) && $rsvp_status[$event['event_id']] == 'decline') ? 'checked' : ''; ?>
                        required> Decline
                </label>

                <button type="submit">RSVP</button>
            </form>
            <?php
                    }
                    ?>

        </div>
        <?php endforeach; ?>

        <?php endif; ?>
    </div>
</body>

</html>