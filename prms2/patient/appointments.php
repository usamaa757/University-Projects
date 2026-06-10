<?php
include 'sidebar.php';
include '../db.php';

// Assuming the patient is logged in and their ID is stored in session
$patient_id = $_SESSION['patient_id'];

// Fetch appointments for the logged-in patient
$query = "
    SELECT a.appointment_id, a.appointment_date, a.status, d.name AS doctor_name, d.specialization, d.email 
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Main Content -->
<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Your Appointments</h2>

        <?php if ($result->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Email</th>
                        <th>Appointment Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['appointment_id']); ?></td>
                        <td><?= htmlspecialchars($row['doctor_name']); ?></td>
                        <td><?= htmlspecialchars($row['specialization']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= date('d M, Y', strtotime($row['appointment_date'])); ?></td>
                        <td>
                            <?php if ($row['status'] == 'Completed'): ?>
                            <span class="badge bg-success">Completed</span>
                            <?php elseif ($row['status'] == 'Cancelled'): ?>
                            <span class="badge bg-danger">Cancelled</span>
                            <?php elseif ($row['status'] == 'Scheduled'): ?>
                            <span class="badge bg-primary">Scheduled</span>
                            <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="alert alert-warning" role="alert">
            You have no upcoming appointments.
        </div>
        <?php } ?>
    </div>
</div>

</body>

</html>