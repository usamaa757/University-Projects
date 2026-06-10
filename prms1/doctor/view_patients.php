<?php
include 'header.php';
include '../config/database.php';

$doctor_id = $_SESSION['user_id']; // Doctor's session ID

// Get patients who have appointments with this doctor
$sql = "SELECT DISTINCT p.id AS patient_id, p.name AS patient_name, p.disease, p.age
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5 border rounded shadow p-4">
    <h3 class="text-center mb-4">👨‍⚕️ My Patients</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Patient Name</th>
                <th>Disease</th>
                <th>Age</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['disease']) ?></td>
                <td><?= htmlspecialchars($row['age']) ?></td>
                <td>
                    <a href="add_treatment.php?patient_id=<?= $row['patient_id'] ?>" class="btn btn-success btn-sm">Add
                        Treatment</a>
                    <a href="patient_medical_records.php?patient_id=<?= $row['patient_id'] ?>"
                        class="btn btn-primary btn-sm">View History</a>
                    <a href="test.php?patient_id=<?= $row['patient_id'] ?>" class="btn btn-primary btn-sm">Add
                        Test</a>
                    <a href="view_test.php?patient_id=<?= $row['patient_id'] ?>" class="btn btn-primary btn-sm">View
                        Test</a>
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