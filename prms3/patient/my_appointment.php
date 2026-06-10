<?php
include 'header.php';

include '../config/database.php';


$patient_id = $_SESSION['user_id'];

// Fetch appointments for the logged-in patient
$sql = "SELECT a.appointment_date, a.id, a.status, a.confirmed_date, d.name AS doctor_name, d.department
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">📋 My Appointments</h2>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Appointment #</th>
                    <th>Apply Date</th>
                    <th>Confirm Date</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($row['confirmed_date']) ?></td>
                    <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'Pending'): ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($row['status'] === 'Confirmed'): ?>
                        <span class="badge bg-success">Confirmed</span>
                        <?php elseif ($row['status'] === 'Cancelled'): ?>
                        <span class="badge bg-danger">Cancelled</span>
                        <?php else: ?>
                        <?= htmlspecialchars($row['status']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No appointments found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center">
            <a href="patient_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>