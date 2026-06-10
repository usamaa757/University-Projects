<?php
// Include the database connection file
include '../db_connection.php';
include 'header.php';


$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = htmlspecialchars(trim($_POST['location']));
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Validate input
    if (empty($name)) {
        $errors[] = "Event name is required.";
    }
    if (empty($event_date)) {
        $errors[] = "Event date is required.";
    }
    if (empty($event_time)) {
        $errors[] = "Event time is required.";
    }

    if (empty($errors)) {
        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO events (name, description, event_date, event_time, location, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $description, $event_date, $event_time, $location, $user_id);

        if ($stmt->execute()) {
            $success = "Event created successfully!";
            // Clear form fields
            $name = $description = $event_date = $event_time = $location = '';
        } else {
            $errors[] = "Error creating event: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<div class="container mt-4 round border shadow p-0" style="max-width: 600px;">
    <!-- Header -->
    <div class="bg-primary text-white">
        <h3 class="p-2">Create New Event</h3>

    </div>

    <!-- Event Creation Form -->
    <div class="container mt-4">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Event Name<span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    value="<?php echo isset($name) ? $name : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"
                    rows="4"><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="event_date">Date<span class="text-danger">*</span></label>
                <input type="date" name="event_date" id="event_date" class="form-control"
                    value="<?php echo isset($event_date) ? $event_date : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="event_time">Time<span class="text-danger">*</span></label>
                <input type="time" name="event_time" id="event_time" class="form-control"
                    value="<?php echo isset($event_time) ? $event_time : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control"
                    value="<?php echo isset($location) ? $location : ''; ?>">
            </div>
            <div class="text-center mb-3">
                <button type="submit" class="btn text-white bg-primary">Create Event</button>

            </div>
        </form>
    </div>
</div>

</body>

</html>