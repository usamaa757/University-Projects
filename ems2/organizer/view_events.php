<?php
include 'header.php';
include '../db.php';

// Fetch events based on the role
$events = [];
if ($_SESSION['role'] === 'organizer') {
    // Fetch events created by the organizer
    $organizer_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE organizer_id = ?");
    $stmt->bind_param("i", $organizer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Fetch events that the attendee has RSVP'd for
    $attendee_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT events.*, rsvps.status FROM events JOIN rsvps ON events.event_id = rsvps.event_id WHERE rsvps.attendee_id = ?");
    $stmt->bind_param("i", $attendee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Your Events</h2>

    <?php if (empty($events)): ?>
    <p class="text-center">No events found.</p>
    <?php else: ?>
    <div class="d-flex flex-row overflow-auto gap-3 px-2">
        <?php foreach ($events as $event): ?>
        <div class="card" style="min-width: 300px; flex: 0 0 auto;">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0"><?= htmlspecialchars($event['event_name']) ?></h5>
            </div>
            <div class="card-body">
                <p class="card-text"><?= htmlspecialchars($event['event_description']) ?></p>
                <p class="fw-bold mb-1">Date: <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                <p class="card-text"><?= htmlspecialchars($event['location']) ?></p>
            </div>
            <div class="card-footer text-center">
                <?php if ($_SESSION['role'] === 'organizer'): ?>
                <a href="edit_event.php?event_id=<?= $event['event_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_event.php?event_id=<?= $event['event_id'] ?>"
                    onclick="return confirm('Are you sure to delete this event?')"
                    class="btn btn-danger btn-sm">Delete</a>
                <?php else: ?>
                <span class="badge bg-info text-dark"><?= htmlspecialchars(ucfirst($event['status'])) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>


</body>

</html>