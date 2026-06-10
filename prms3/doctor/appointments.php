<?php
include 'header.php';
include '../config/database.php';

$doctor_id = $_SESSION['user_id']; // Ensure doctor is logged in

// Handle appointment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
    $date = $_POST['confirmed_date'];


    $stmt = $conn->prepare("UPDATE appointments SET status = ?, confirmed_date = ? WHERE id = ? AND doctor_id = ?");
    $stmt->bind_param("ssii", $status, $date, $appointment_id, $doctor_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch appointments for the doctor
$sql = "SELECT a.id, a.appointment_date, p.name AS patient_name, a.status 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ? ORDER BY a.appointment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5 border rounded shadow p-4">
    <h3 class="mb-4 text-center">📅 Manage Appointments</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Patient</th>
                <th>Original Date</th>
                <th>Status</th>
                <th>Confirm Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td class="badge bg-info m-2 text-white"><?= htmlspecialchars($row['status']) ?></td>
                <td>


                    <?php if ($row['status'] === 'Accepted'): ?>
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="confirmed_date"
                            value="<?= htmlspecialchars($row['appointment_date']) ?>">
                        
                        <button name="status" value="Completed" class="btn btn-success btn-sm">Mark as
                            Completed</button>
                            <a href="add_treatment.php?patient_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Add Treament</a>
                    </form>

                    <?php elseif ($row['status'] === 'Completed'): ?>
                    <span class="badge bg-success"><?= $row['status'] ?></span>

                    <?php elseif ($row['status'] === 'Cancelled'): ?>
                    <span class="badge bg-warning"><?= $row['status'] ?></span>

                    <?php else: ?>
                    <span class="badge bg-secondary"><?= $row['status'] ?></span>
                    <?php endif; ?>

                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$conn->close();
?>