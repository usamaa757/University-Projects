<?php

include 'header.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $attendee_id = $_SESSION['user_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO rsvps (attendee_id, event_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");
    $stmt->bind_param("iiss", $attendee_id, $event_id, $status, $status);

    if ($stmt->execute()) {
        echo "<script>alert('RSVP successful!'); window.location.href='view_rsvp_events.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Base query
$sql = "SELECT * FROM events WHERE event_date >= CURDATE()";
$params = [];
$types = "";

// Add conditions based on search input
if (!empty($keyword)) {
    $sql .= " AND (event_name LIKE ? OR event_description LIKE ? OR location LIKE ?)";
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $types .= "sss";
}

if (!empty($date)) {
    $sql .= " AND event_date = ?";
    $params[] = $date;
    $types .= "s";
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);


$stmt->close();
$conn->close();


if (empty($events)): ?>
<p class="text-center">No events found.</p>
<?php else: ?>
<div class="container mt-5">

    <form method="GET" class="mb-4 mx-auto" action="search_event.php">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" name="keyword" class="form-control" placeholder="Search by event name or description"
                    value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            </div>
            <div class="col-md-4">
                <input type="date" name="date" class="form-control"
                    value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php foreach ($events as $event): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0"><?= htmlspecialchars($event['event_name']) ?></h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><?= htmlspecialchars($event['event_description']) ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                    <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                </div>
                <div class="card-footer text-center bg-light">
                    <form action="view_events.php" method="post">
                        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">RSVP Status</label>
                            <select name="status" required class="form-select">
                                <option value="attend">Attend</option>
                                <option value="maybe">Maybe</option>
                                <option value="decline">Decline</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-success btn-sm ms-2">RSVP</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>