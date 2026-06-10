<?php
include 'header.php';
include '../db.php';

if (!isset($_GET['event_id'])) {
    echo "<div class='alert alert-danger'>Invalid event.</div>";
    exit();
}

$event_id = $_GET['event_id'];

// Fetch RSVP list with attendee names and statuses
$sql = "
    SELECT s.username AS attendee_name, r.status 
    FROM rsvps r 
    JOIN attendees s ON r.attendee_id = s.attendee_id 
    WHERE r.event_id = ? ORDER BY r.status ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$rsvps = [];
while ($row = $result->fetch_assoc()) {
    $rsvps[] = $row;
}
?>

<div class="container mt-5">
    <h3 class="mb-4">RSVP Details</h3>

    <?php if (empty($rsvps)): ?>
    <div class="alert alert-info">No RSVPs for this event yet.</div>
    <?php else: ?>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Attendee Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rsvps as $rsvp): ?>
            <tr>
                <td><?= htmlspecialchars($rsvp['attendee_name']) ?></td>
                <td>
                    <?php
                            $status = $rsvp['status'];
                            $badge = match ($status) {
                                'attend' => 'success',
                                'maybe' => 'warning',
                                'decline' => 'danger',
                                default => 'secondary'
                            };
                            ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>