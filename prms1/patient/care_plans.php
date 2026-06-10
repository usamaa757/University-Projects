<?php
include 'header.php';
include '../config/database.php';

$patient_id = $_GET['patient_id'] ?? 0;

// Fetch patient info
$patient = $conn->query("SELECT name FROM patients WHERE id = $patient_id")->fetch_assoc();

// Fetch care plans assigned to this patient
$stmt = $conn->prepare("SELECT cp.id as plan_id, cp.title, cp.description, pcp.assigned_at 
                        FROM patient_care_plans pcp 
                        JOIN care_plans cp ON cp.id = pcp.care_plan_id 
                        WHERE pcp.patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4 shadow rounded border p-4">
    <h3 class="text-center">Care Plans for <?= htmlspecialchars($patient['name']) ?></h3>

    <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <strong><?= htmlspecialchars($row['title']) ?></strong>
            <div class="small">Assigned on: <?= date("d M, Y", strtotime($row['assigned_at'])) ?></div>
        </div>
        <div class="card-body">
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>

            <?php
                    // Fetch steps for this care plan
                    $steps_stmt = $conn->prepare("SELECT step_number, step_description FROM care_plan_steps WHERE care_plan_id = ? ORDER BY step_number ASC");
                    $steps_stmt->bind_param("i", $row['plan_id']);
                    $steps_stmt->execute();
                    $steps_result = $steps_stmt->get_result();
                    ?>

            <?php if ($steps_result->num_rows > 0): ?>
            <h5>Steps:</h5>
            <ol class="list-group list-group-numbered">
                <?php while ($step = $steps_result->fetch_assoc()): ?>
                <li class="list-group-item">

                    <?= nl2br(htmlspecialchars($step['step_description'])) ?>
                </li>
                <?php endwhile; ?>
            </ol>
            <?php else: ?>
            <div class="alert alert-warning">No steps defined for this care plan.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="alert alert-info">No care plans assigned to this patient.</div>
    <?php endif; ?>
</div>