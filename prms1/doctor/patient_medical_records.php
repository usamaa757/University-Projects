<?php
include 'header.php';
include '../config/database.php';

// Get patient ID from URL
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo "<div class='alert alert-danger'>Patient ID is required.</div>";
    exit;
}

// Fetch patient details
$patient_stmt = $conn->prepare("SELECT name FROM patients WHERE id = ?");
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient = $patient_result->fetch_assoc();
$patient_stmt->close();

if (!$patient) {
    echo "<div class='alert alert-warning'>Patient not found.</div>";
    exit;
}

// Fetch treatment history with doctor and medicine details
$treat_stmt = $conn->prepare("
    SELECT 
        t.treatment_date, 
        t.treatment, 
        GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ', ') AS medicine_names,
        d.name AS doctor_name
    FROM treatments t
    JOIN medicines m ON t.medicine_id = m.id
    JOIN doctors d ON t.doctor_id = d.id
    WHERE t.patient_id = ?
    GROUP BY t.treatment_date, t.treatment, d.name
    ORDER BY t.treatment_date DESC
");

$treat_stmt->bind_param("i", $patient_id);
$treat_stmt->execute();
$treat_result = $treat_stmt->get_result();
?>

<div class="container mt-5 border rounded shadow p-4">
    <h3 class="mb-4 text-center">Medical Record for <?= htmlspecialchars($patient['name']) ?></h3>

    <?php if ($treat_result->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Doctor</th>
                <th>Medicine</th>
                <th>Treatment / Instructions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $treat_result->fetch_assoc()): ?>
            <tr>
                <td><?= date('d M Y', strtotime($row['treatment_date'])) ?></td>
                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                <td><?= htmlspecialchars($row['medicine_names']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['treatment'])) ?></td>
            </tr>
            <?php endwhile; ?>

        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-info">No treatment records found for this patient.</div>
    <?php endif; ?>
</div>