<?php
include 'header.php';
include '../db.php';

$organizer_id = $_SESSION['user_id'];

// Fetch organizer info
$stmt = $conn->prepare("SELECT * FROM organizers WHERE organizer_id = ?");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
$organizer = $result->fetch_assoc();

// Fetch total events count
$event_stmt = $conn->prepare("SELECT COUNT(*) AS total_events FROM events WHERE organizer_id = ?");
$event_stmt->bind_param("i", $organizer_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();
$event_data = $event_result->fetch_assoc();
$total_events = $event_data['total_events'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Organizer Dashboard</title>
    <style>
    .dashboard-container {
        max-width: 900px;
        margin: 60px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header {
        background-color: #000000;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        color: white;
        text-align: center;
    }

    .dashboard-body {
        padding: 20px;
    }

    .dashboard-body p.lead {
        font-size: 20px;
        margin-bottom: 15px;
        text-align: center;
    }

    .event-count {
        text-align: center;
        font-size: 16px;
        margin-bottom: 25px;
        color: #333;
    }

    .dashboard-links {
        display: flex;
        justify-content: space-around;
        gap: 20px;
        flex-wrap: wrap;
    }

    .dashboard-link {
        flex: 1 1 200px;
        padding: 20px;
        text-align: center;
        background-color: #f0f4f8;
        color: #000;
        text-decoration: none;
        border-radius: 12px;
        transition: 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        font-weight: bold;
    }

    .dashboard-link i {
        font-size: 24px;
        display: block;
        margin-bottom: 10px;
    }

    .dashboard-link:hover {
        background-color: #d9efff;
        transform: translateY(-2px);
    }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Welcome, <?= htmlspecialchars($organizer['username']) ?>!</h2>
        </div>
        <div class="dashboard-body">
            <p class="lead">Your Organizer Dashboard</p>
            <p class="event-count">You have created <strong><?= $total_events ?></strong> event(s).</p>
            <div class="dashboard-links">
                <a href="create_event.php" class="dashboard-link">
                    <i>📅</i>
                    Create New Event
                </a>
                <a href="my_event.php" class="dashboard-link">
                    <i>✅</i>
                    View Your Events
                </a>
                <a href="manage_rsvp.php" class="dashboard-link">
                    <i>👥</i>
                    Manage RSVPs
                </a>
            </div>
        </div>
    </div>

</body>

</html>