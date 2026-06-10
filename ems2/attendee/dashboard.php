<?php
include 'header.php';
include '../db.php';

// Fetch attendee data
$attendee_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM attendees WHERE attendee_id = ?");
$stmt->bind_param("i", $attendee_id);
$stmt->execute();
$result = $stmt->get_result();
$attendee = $result->fetch_assoc();
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white">
            <h3 class="mb-0 text-center">Welcome, <?= htmlspecialchars($attendee['username']) ?>!</h3>
        </div>
        <div class="card-body">
            <p class="lead">This is your attendee dashboard. You can browse events and manage your RSVPs.</p>

            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <a href="view_events.php" class="btn btn-outline-primary btn-lg w-100">
                        <i class="bi bi-calendar-event-fill me-2"></i> Browse Events
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="view_rsvp_events.php" class="btn btn-outline-success btn-lg w-100">
                        <i class="bi bi-check2-square me-2"></i> View Your RSVPs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



</body>

</html>