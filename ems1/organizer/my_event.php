<?php
include 'header.php';
include '../db.php';

// Fetch events based on the role
$events = [];
if ($_SESSION['role'] === 'organizer') {
    $organizer_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE organizer_id = ?");
    $stmt->bind_param("i", $organizer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $attendee_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT events.*, rsvps.status FROM events JOIN rsvps ON events.event_id = rsvps.event_id WHERE rsvps.attendee_id = ?");
    $stmt->bind_param("i", $attendee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Events</title>
    <style>
    .event-container {
        max-width: 1000px;
        margin: 60px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 14px 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #000000;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f3f8fb;
    }

    .action-btn {
        padding: 6px 12px;
        margin: 2px;
        font-size: 14px;
        text-decoration: none;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-edit {
        background-color: #ffc107;
        color: #000;
    }

    .btn-delete {
        background-color: #dc3545;
        color: #fff;
    }

    .badge-status {
        display: inline-block;
        padding: 5px 10px;
        background-color: #e0f7fa;
        color: #006064;
        border-radius: 6px;
        font-weight: bold;
    }

    .no-events {
        text-align: center;
        font-style: italic;
        color: #666;
    }
    </style>
</head>

<body>

    <div class="event-container">
        <h2>Your Events</h2>

        <?php if (empty($events)): ?>
        <p class="no-events">No events found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Location</th>
                    <?php if ($_SESSION['role'] === 'organizer'): ?>
                    <th>Actions</th>
                    <?php else: ?>
                    <th>Status</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['event_name']) ?></td>
                    <td><?= htmlspecialchars($event['event_description']) ?></td>
                    <td><?= date('F d, Y', strtotime($event['event_date'])) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <?php if ($_SESSION['role'] === 'organizer'): ?>
                    <td>
                        <a href="edit_event.php?event_id=<?= $event['event_id'] ?>" class="action-btn btn-edit">Edit</a>
                        <a href="delete_event.php?event_id=<?= $event['event_id'] ?>" class="action-btn btn-delete"
                            onclick="return confirm('Are you sure you want to delete this event?');">
                            Delete
                        </a>
                    </td>
                    <?php else: ?>
                    <td><span class="badge-status"><?= ucfirst(htmlspecialchars($event['status'])) ?></span></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</body>

</html>