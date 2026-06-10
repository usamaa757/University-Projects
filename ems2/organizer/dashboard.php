<?php
include 'header.php';
include '../db.php';

// Fetch organizer data
$organizer_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM organizers WHERE organizer_id = ?");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
$organizer = $result->fetch_assoc();
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white">
            <h3 class="mb-0 text-center">Welcome, <?= htmlspecialchars($organizer['username']) ?>!</h3>
        </div>
        <div class="card-body">
            <p class="lead">Your Organizer Dashboard</p>
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <a href="create_event.php" class="btn btn-outline-success btn-lg w-100">
                        <i class="bi bi-calendar-plus me-2"></i>Create New Event
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="view_events.php" class="btn btn-outline-primary btn-lg w-100">
                        <i class="bi bi-calendar-check me-2"></i>View Your Events
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="manage_rsvp.php" class="btn btn-outline-warning btn-lg w-100">
                        <i class="bi bi-people-fill me-2"></i>Manage RSVPs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>