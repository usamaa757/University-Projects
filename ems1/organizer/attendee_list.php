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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP Details</title>
    <style>
    .rvsp-container {
        width: 80%;
        margin: 40px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .alert {
        padding: 15px;
        margin: 10px 0;
        border-radius: 5px;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #000000;
        color: white;
        text-transform: uppercase;
    }

    td {
        background-color: #fafafa;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .badge {
        display: inline-block;
        padding: 5px 10px;
        font-size: 14px;
        font-weight: bold;
        border-radius: 3px;
    }

    .badge.success {
        background-color: #28a745;
        color: #fff;
    }

    .badge.warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge.danger {
        background-color: #dc3545;
        color: #fff;
    }

    .badge.secondary {
        background-color: #6c757d;
        color: #fff;
    }
    </style>
</head>

<body>

    <div class="rvsp-container">
        <h3>RSVP Details</h3>

        <?php if (empty($rsvps)): ?>
        <div class="alert alert-info">No RSVPs for this event yet.</div>
        <?php else: ?>
        <table>
            <thead>
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
                        <span class="badge <?= $badge ?>"><?= ucfirst($status) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</body>

</html>