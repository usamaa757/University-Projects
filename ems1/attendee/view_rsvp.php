<?php
include 'header.php';
include '../db.php';

// Fetch events that the attendee has RSVP'd for
$events = [];

$attendee_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT events.*, rsvps.status FROM events JOIN rsvps ON events.event_id = rsvps.event_id WHERE rsvps.attendee_id = ?");
$stmt->bind_param("i", $attendee_id);
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
?>

<style>
.view-event-container {
    max-width: 900px;
    margin: auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th,
td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #222;
    color: white;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    color: white;
    font-size: 0.9em;
}

.bg-success {
    background-color: #28a745;
}

.bg-warning {
    background-color: #ffc107;
    color: #333;
}

.bg-danger {
    background-color: #dc3545;
}

.text-center {
    text-align: center;
}
</style>

<div class="view-event-container">
    <h2>RSVPs Events</h2>

    <?php if (empty($events)): ?>
    <p class="text-center">No events found.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Description</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['event_name']) ?></td>
                <td><?= htmlspecialchars($event['event_description']) ?></td>
                <td><?= date('F d, Y', strtotime($event['event_date'])) ?></td>
                <td>
                    <?php
                            if ($event['status'] == 'attend') {
                                echo '<span class="badge bg-success">Attending</span>';
                            } elseif ($event['status'] == 'maybe') {
                                echo '<span class="badge bg-warning">Maybe</span>';
                            } elseif ($event['status'] == 'decline') {
                                echo '<span class="badge bg-danger">Declined</span>';
                            }
                            ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>

</html>