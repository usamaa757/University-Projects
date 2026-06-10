<?php
include 'header.php';
include '../db.php';

// Check if the user is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_description = $_POST['event_description'];
    $organizer_id = $_SESSION['user_id'];  // Organizer ID from session
    $location = $_POST['location'];
    // Insert event into the database
    $stmt = $conn->prepare("INSERT INTO events (event_name, location, event_date, event_description, organizer_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $event_name, $location, $event_date, $event_description, $organizer_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event created successfully!'); window.location.href='view_events.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
}
?>


<!-- Create Event Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center">Create Event</h3>
                    <form action="create_event.php" method="POST">
                        <div class="mb-3">
                            <label>Event Name</label>
                            <input type="text" name="event_name" required class="form-control"
                                placeholder="Enter event name">
                        </div>
                        <div class="mb-3">
                            <label>Event Location</label>
                            <input type="text" name="location" required class="form-control"
                                placeholder="Enter event name">
                        </div>
                        <div class="mb-3">
                            <label>Event Date</label>
                            <input type="date" name="event_date" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Event Description</label>
                            <textarea name="event_description" required class="form-control"
                                placeholder="Enter event description"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>