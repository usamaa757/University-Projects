<?php
include 'sidebar.php';
include '../db.php';

if (isset($_POST['schedule'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['new_date'];


    $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?,  status = 'Scheduled' WHERE appointment_id = ? ");
    $stmt->bind_param("si", $new_date, $appointment_id);
    $stmt->execute();
    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully!'); window.location.href='appointment_list.php';</script>";
    } else {
        echo "<script>alert('Error updated status.'); window.history.back();</script>";
        exit;
    }
}
if (isset($_POST['cancel'])) {
    $appointment_id = $_POST['appointment_id'];

    $stmt = $conn->prepare("UPDATE appointments SET  status = 'Cancelled' WHERE appointment_id = ?");
    $stmt->bind_param("i",  $appointment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully!'); window.location.href='appointments.php';</script>";
    } else {
        echo "<script>alert('Error updated status.'); window.history.back();</script>";
        exit;
    }
} elseif (isset($_POST['complete'])) {
    $appointment_id = $_POST['appointment_id'];

    $stmt = $conn->prepare("UPDATE appointments SET  status = 'Completed' WHERE appointment_id = ?");
    $stmt->bind_param("i",  $appointment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully!'); window.location.href='appointments.php';</script>";
    } else {
        echo "<script>alert('Error updated status.'); window.history.back();</script>";
        exit;
    }
}

$doctor_id = $_SESSION['doctor_id'];

$query = "SELECT a.appointment_id, a.appointment_date, a.status, p.disease, p.age, p.name AS patient_name
          FROM appointments a
          JOIN patients p ON a.patient_id = p.patient_id
          WHERE a.doctor_id = ?
          ORDER BY a.appointment_date";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Your Appointments</h2>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Patient</th>
                        <th>Age</th>
                        <th>Disease</th>
                        <th>Appointment Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['patient_name']); ?></td>
                        <td><?= htmlspecialchars($row['age']); ?></td>
                        <td><?= htmlspecialchars($row['disease']); ?></td>


                        <td><?= htmlspecialchars($row['appointment_date']); ?></td>
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
                        <td>
                            <?php if ($row['status'] == 'Cancelled') {

                                ?>
                            <span class="badge bg-danger">Cancelled</span>
                            <?php
                                } elseif ($row['status'] == 'Scheduled') { ?>

                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="appointment_id" value="<?= $row['appointment_id']; ?>">
                                <button type="submit" name="complete" class="btn btn-sm btn-success">Completed</button>
                            </form>
                            <?php
                                } elseif ($row['status'] == 'Pending') { ?>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="appointment_id" value="<?= $row['appointment_id']; ?>">
                                <input type="date" name="new_date" value="<?= $row['appointment_date']; ?>" required>

                                <button type="submit" name="schedule" class="btn btn-sm btn-warning">Schedule</button>
                            </form>
                            <!-- Cancel Button -->
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="appointment_id" value="<?= $row['appointment_id']; ?>">
                                <button type="submit" name="cancel" class="btn btn-sm btn-danger">Cancel</button>
                            </form>
                            <?php } else { ?>
                            <span class="badge bg-success">Completed</span>
                            <?php } ?>

                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>