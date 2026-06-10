<?php


include 'header.php';

include '../config/database.php';


// Fetch doctors and availability
$sql = "SELECT id, name, department, availability FROM doctors";
$result = $conn->query($sql);
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

?>



<div class="container mt-5">
    <h2 class="mb-4 text-center">👨‍⚕️ Available Doctors for Appointment</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Availability</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td>
                    <?php if ($row['availability'] === 'Available'): ?>
                    <span class="badge bg-success">Available</span>
                    <?php else: ?>
                    <span class="badge bg-secondary">Unavailable</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                            $alreadyBooked = false;

                            if (isset($_SESSION['user_id'])) {
                                $checkStmt = $conn->prepare("SELECT id FROM appointments WHERE patient_id = ? AND doctor_id = ? AND status IN ('Pending')");
                                $checkStmt->bind_param("ii", $_SESSION['user_id'], $row['id']);
                                $checkStmt->execute();
                                $checkResult = $checkStmt->get_result();
                                $alreadyBooked = $checkResult->num_rows > 0;
                                $checkStmt->close();
                            }
                            ?>

                    <?php if ($row['availability'] === 'Available'): ?>
                    <?php if ($alreadyBooked): ?>
                    <button class="btn btn-warning btn-sm" disabled>Already Booked</button>
                    <?php else: ?>
                    <a href="book_appointment.php?doctor_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Book</a>
                    <?php endif; ?>
                    <?php else: ?>
                    <button class="btn btn-outline-secondary btn-sm" disabled>Not Available</button>
                    <?php endif; ?>
                </td>

            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No doctors found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>

</html>

<?php $conn->close(); ?>