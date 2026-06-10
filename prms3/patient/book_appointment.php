<?php
include 'header.php';

include '../config/database.php';


// Get doctor ID from query parameter
$doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;

// Fetch doctor details
$doctor = null;
if ($doctor_id > 0) {
    $stmt = $conn->prepare("SELECT id, name, department FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $doctor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_date = $_POST['date'];
    $patient_id = $_SESSION['user_id'];

    // Insert appointment
    // Check if appointment already exists
    $check = $conn->prepare("SELECT id FROM appointments WHERE patient_id = ? AND doctor_id = ? AND appointment_date = ?");
    $check->bind_param("iis", $patient_id, $doctor_id, $appointment_date);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "You have already booked this appointment.";
    } else {
        // Insert appointment
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $patient_id, $doctor_id, $appointment_date);
        if ($stmt->execute()) {
            $success = "Appointment booked successfully!";
        } else {
            $error = "Error booking appointment.";
        }
        $stmt->close();
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>📅 Book Appointment</h2>

        <?php if ($doctor): ?>
        <div class="mb-4">
            <p><strong>Doctor:</strong> <?= htmlspecialchars($doctor['name']) ?></p>
            <p><strong>Department:</strong> <?= htmlspecialchars($doctor['department']) ?></p>
        </div>

        <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="date" class="form-label">Appointment Date</label>
                <input type="date" name="date" id="date" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>

            <button type="submit" class="btn btn-primary">Book Appointment</button>
            <a href="patient_appointments.php" class="btn btn-secondary">Back</a>
        </form>

        <?php else: ?>
        <div class="alert alert-warning">Invalid doctor selected.</div>
        <a href="patient_appointments.php" class="btn btn-secondary">Back</a>
        <?php endif; ?>
    </div>
</body>

</html>

<?php $conn->close(); ?>