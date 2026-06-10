<?php
include 'header.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
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

// Search functionality
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT * FROM events WHERE event_date IS NOT NULL";
$params = [];
$types = "";

if (!empty($keyword)) {
    $sql .= " AND (event_name LIKE ? OR event_description LIKE ? OR location LIKE ?)";
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $types .= "sss";
}

if (!empty($date)) {
    $sql .= " AND event_date = ?";
    $params[] = $date;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search & RSVP Events</title>
    <style>
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

    .rsvp-form {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rsvp-form label {
        margin-right: 15px;
    }

    .rsvp-form select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
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
        <?php if (isset($message)): ?>
        <div class="message-container <?= $msg_type; ?>">
            <?= htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="GET" class="search-form" action="">
            <input type="text" name="keyword" placeholder="Search by event name or description"
                value="<?= htmlspecialchars($keyword) ?>">
            <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Event List -->
        <?php if (empty($events)): ?>
        <p class="no-events">No events found.</p>
        <?php else: ?>
        <?php foreach ($events as $event): ?>
        <div class="event-card">
            <h3><?= htmlspecialchars($event['event_name']) ?></h3>
            <p><?= htmlspecialchars($event['event_description']) ?></p>
            <p><strong>Date:</strong> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>

            <!-- RSVP Form -->
            <?php if (date('Y, m, d') > $event['event_date']) {
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
            <?php } else {
                        echo '<div class="message-contianer">Event has already passed.</div>';
                    } ?>
        </div>

    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    </div>

</body>

</html>