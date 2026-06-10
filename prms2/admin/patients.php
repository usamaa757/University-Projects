<?php
include 'sidebar.php';
include '../db.php';

if (isset($_GET['delete'])) {
    $patient_id = $_GET['delete'];
    $conn->query("DELETE FROM patients WHERE patient_id = $patient_id");
    echo "<script>alert('Patient deleted successfully');window.location='patients.php';</script>";
}
$stmt = $conn->prepare("SELECT patient_id, name, age, gender, contact FROM patients ORDER BY patient_id DESC");
$stmt->execute();
$result = $stmt->get_result();

?>


<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Manage Patients</h2>

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
                            <a href="edit_patient.php?id=<?= $row['patient_id']; ?>"
                                class="btn btn-sm btn-warning me-1">
                                ✏️ Edit
                            </a>
                            <a href="patients.php?delete=<?= $row['patient_id']; ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this patient?');">
                                🗑️ Delete
                            </a>
                            <a href="medical_history.php?patient_id=<?= $row['patient_id'] ?>"
                                class="btn btn-sm btn-success">
                                Medical History
                            </a>

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


</html>