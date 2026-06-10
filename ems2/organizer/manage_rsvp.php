<?php
include 'header.php';
include '../db.php';


$sql = "
    SELECT 
        e.event_id,
        e.event_name,
        e.event_description,
        e.event_date,
        SUM(CASE WHEN r.status = 'attend' THEN 1 ELSE 0 END) AS attend_count,
        SUM(CASE WHEN r.status = 'maybe' THEN 1 ELSE 0 END) AS maybe_count,
        SUM(CASE WHEN r.status = 'decline' THEN 1 ELSE 0 END) AS decline_count,
        COUNT(r.status) AS total_rsvp
    FROM events e
    LEFT JOIN rsvps r ON e.event_id = r.event_id
    WHERE e.organizer_id = ?
    GROUP BY e.event_id
    ORDER BY attend_count DESC, maybe_count DESC, decline_count ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Event Attendance Summary</h2>
    <div class="row">
        <?php while ($event = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?= htmlspecialchars($event['event_name']) ?></h5>
                    <small><?= date('F d, Y', strtotime($event['event_date'])) ?></small>
                </div>
                <div class="card-body">
                    <p><span class="badge bg-success">Attending:</span> <?= $event['attend_count'] ?></p>
                    <p><span class="badge bg-warning text-dark">Maybe:</span> <?= $event['maybe_count'] ?></p>
                    <p><span class="badge bg-danger">Declined:</span> <?= $event['decline_count'] ?></p>
                    <hr>
                    <p><strong>Total RSVPs:</strong> <?= $event['total_rsvp'] ?></p>
                </div>
                <div class="card-footer text-center">
                    <a href="view_event_rsvps.php?event_id=<?= $event['event_id'] ?>"
                        class="btn btn-outline-primary btn-sm">View RSVPs</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>

</html>