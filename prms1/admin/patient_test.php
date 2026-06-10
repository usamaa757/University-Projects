<?php
include 'header.php';
include '../config/database.php';


// Get patient ID from query string
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo "<div class='alert alert-warning'>No patient selected.</div>";
    exit;
}

// Fetch assigned tests
$test_stmt = $conn->prepare("
    SELECT pt.assigned_date, t.name AS test_name, pt.status, pt.remarks, d.name AS doctor_name
    FROM patient_tests pt
    JOIN tests t ON pt.test_id = t.id
    JOIN doctors d ON pt.doctor_id = d.id
    WHERE pt.patient_id = ?
    ORDER BY pt.assigned_date DESC
");
$test_stmt->bind_param("i", $patient_id);
$test_stmt->execute();
$test_result = $test_stmt->get_result();
?>

<div class="container mt-5 border rounded shadow p-4">
    <h3 class="text-center mb-4">🧪 Assigned Tests for Patient ID: <?= htmlspecialchars($patient_id) ?></h4>
        <?php if ($test_result->num_rows > 0): ?>
        <table class=" table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Test</th>
                    <th>Doctor</th>
                    <th>Remarks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $test_result->fetch_assoc()): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($row['assigned_date'])) ?></td>
                    <td><?= htmlspecialchars($row['test_name']) ?></td>
                    <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['remarks'])) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info">No tests assigned to this patient.</div>
        <?php endif; ?>
</div>