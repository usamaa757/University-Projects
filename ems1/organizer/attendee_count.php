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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendance Summary</title>
    <style>
    .attendee-container {
        width: 90%;
        margin: 20px auto;
    }

    h2 {
        text-align: center;
        margin-bottom: 40px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #000000;
        color: #fff;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 14px;
    }

    .badge.bg-success {
        background-color: #28a745;
        color: #fff;
    }

    .badge.bg-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge.bg-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .btn {
        padding: 6px 12px;
        font-size: 14px;
        border: 1px solid #007bff;
        color: #007bff;
        background-color: transparent;
        border-radius: 4px;
        text-decoration: none;
    }

    .btn:hover {
        background-color: #007bff;
        color: white;
    }
    </style>
</head>

<body>

    <div class="attendee-container">
        <h2>Event Attendance Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Attending</th>
                    <th>Maybe</th>
                    <th>Declined</th>
                    <th>Total RSVPs</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($event['event_name']) ?></td>
                    <td><?= date('F d, Y', strtotime($event['event_date'])) ?></td>
                    <td><span class="badge bg-success"><?= $event['attend_count'] ?></span></td>
                    <td><span class="badge bg-warning"><?= $event['maybe_count'] ?></span></td>
                    <td><span class="badge bg-danger"><?= $event['decline_count'] ?></span></td>
                    <td><?= $event['total_rsvp'] ?></td>
                    <td><a href="attendee_list.php?event_id=<?= $event['event_id'] ?>" class="btn">View
                            Attendee List</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>