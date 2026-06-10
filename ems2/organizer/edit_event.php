<?php
include 'header.php';
include '../db.php';

// Fetch event details
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    if (!$event) {
        echo "<script>alert('Event not found!'); window.location.href='view_events.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Event ID is required'); window.location.href='view_events.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];

    // Update the event in the database
    $update_stmt = $conn->prepare("UPDATE events SET event_name = ?, location = ?, event_description = ?, event_date = ? WHERE event_id = ?");
    $update_stmt->bind_param("ssssi", $event_name, $location, $event_description, $event_date, $event_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Event updated successfully!'); window.location.href='edit_event.php?event_id=$event_id';</script>";
    } else {
        echo "<script>alert('Error updating event: " . $update_stmt->error . "');</script>";
    }
    $update_stmt->close();
    $conn->close();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Edit Event</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" name="event_name" id="event_name" class="form-control"
                                value="<?= htmlspecialchars($event['event_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Event Location</label>
                            <input type="text" name="location" id="location" class="form-control"
                                value="<?= htmlspecialchars($event['location']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="event_description" class="form-label">Event Description</label>
                            <textarea name="event_description" id="event_description" class="form-control" rows="3"
                                required><?= htmlspecialchars($event['event_description']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="event_date" class="form-label">Event Date</label>
                            <input type="date" name="event_date" id="event_date" class="form-control"
                                value="<?= $event['event_date'] ?>" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Event</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        </body>

        </html>