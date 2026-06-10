<?php
include 'header.php';
include '../config/database.php';

// Fetch all patients
$patients_stmt = $conn->prepare("SELECT id, name, email FROM patients ORDER BY name ASC");
$patients_stmt->execute();
$patients_result = $patients_stmt->get_result();
?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">🧍 All Registered Patients</h3>

    <?php if ($patients_result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1;
                while ($row = $patients_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <a href="family_record.php?patient_id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                Family History
                            </a>
                            <a href="view_treatment.php?patient_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                                View Treatments
                            </a>
                            <a href="generate_report.php?patient_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                Add Test Report
                            </a>
                            </a>

                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No patients found.</div>
    <?php endif; ?>
</div>