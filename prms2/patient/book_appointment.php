<?php
include 'sidebar.php';
include '../db.php';

// Fetch doctors from database
$query = "SELECT doctor_id, name, specialization, email FROM doctors";
$result = $conn->query($query);

$patient_id = $_SESSION['patient_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    // Insert appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $patient_id, $doctor_id, $appointment_date);
    if ($stmt->execute()) {
        echo "<script>alert('Appointment successfully booked!'); window.location.href='appointments.php';</script>";
    } else {
        echo "<script>alert('Failed to book the appointment. Please try again later.'); window.history.back();</script>";
        exit;
    }

    $stmt->close();
}
$booked_doctors = [];

$check_query = "SELECT doctor_id FROM appointments WHERE patient_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result_booked = $stmt->get_result();

while ($row = $result_booked->fetch_assoc()) {
    $booked_doctors[] = $row['doctor_id'];
}
$stmt->close();

?>


<!-- Main Content -->
<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Book an Appointment</h2>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['specialization']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td>
                            <?php if (in_array($row['doctor_id'], $booked_doctors)) { ?>
                            <button class="btn btn-secondary" disabled>Appointed</button>
                            <?php } else { ?>
                            <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#bookAppointmentModal" data-doctor-id="<?= $row['doctor_id']; ?>"
                                data-doctor-name="<?= $row['name']; ?>">Book Appointment</button>
                            <?php } ?>

                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Book Appointment Modal -->
    <div class="modal fade" id="bookAppointmentModal" tabindex="-1" aria-labelledby="bookAppointmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookAppointmentModalLabel">Book Appointment with <span
                            id="doctor-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="doctor_id" id="doctor-id">
                        <div class="mb-3">
                            <label for="appointment-date" class="form-label">Select Appointment Date</label>
                            <input type="date" name="appointment_date" class="form-control" id="appointment-date"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var bookAppointmentModal = document.getElementById('bookAppointmentModal');
bookAppointmentModal.addEventListener('show.bs.modal', function(event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var doctorId = button.getAttribute('data-doctor-id');
    var doctorName = button.getAttribute('data-doctor-name');

    // Set the doctor name and doctor id in modal
    document.getElementById('doctor-name').textContent = doctorName;
    document.getElementById('doctor-id').value = doctorId;
});
</script>
</body>

</html>