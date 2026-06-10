<?php
include 'sidebar.php';
include '../db.php';

$doctor_id = $_SESSION['doctor_id'];

$stmt = $conn->prepare("
    SELECT p.patient_id, p.name, p.age, p.gender, p.contact, a.status
    FROM patients p
    INNER JOIN appointments a ON p.patient_id = a.patient_id
    WHERE a.doctor_id = ? AND a.status = 'Scheduled'
    ORDER BY p.patient_id DESC
");


$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center"> Appointment Records</h2>

        <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['patient_id']); ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['age']); ?></td>
                        <td><?= htmlspecialchars($row['gender']); ?></td>
                        <td><?= htmlspecialchars($row['contact']); ?></td>
                        <td>
                            <a href="medical_history.php?patient_id=<?= $row['patient_id'] ?>"
                                class="btn btn-sm btn-success">
                                Medical History
                            </a>
                            <?php if ($row['status'] == 'Scheduled') { ?>
                            <a href="add_treatment.php?patient_id=<?= $row['patient_id'] ?>"
                                class="btn btn-sm btn-primary">
                                Add Treatment
                            </a>
                            <?php } ?>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">No patient records found.</div>
        <?php endif; ?>
    </div>
</div>

</body>

</html>