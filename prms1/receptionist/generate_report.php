<?php
include 'header.php';
include '../config/database.php';

$patient_id = $_GET['patient_id']; // Or from session or form

// Fetch treatments
$treatments = $conn->query("
    SELECT t.*, d.name AS doctor_name 
    FROM treatments t
    JOIN doctors d ON t.doctor_id = d.id
    WHERE t.patient_id = $patient_id 
    ORDER BY t.treatment_date DESC
");

// Fetch tests
$tests = $conn->query("
    SELECT pt.*, t.name, d.name AS doctor_name
    FROM patient_tests pt
    JOIN tests t ON pt.test_id = t.id
    JOIN doctors d ON pt.doctor_id = d.id
    WHERE pt.patient_id = $patient_id
    ORDER BY pt.assigned_date DESC
");


// Fetch instructions
$query = $conn->query("
    SELECT 
        pcp.id AS patient_plan_id,
        pcp.assigned_at,
        pcp.status,
        cp.title AS plan_title,
        cp.description AS plan_description,
        cps.step_number,
        cps.step_description
    FROM patient_care_plans pcp
    JOIN care_plans cp ON pcp.care_plan_id = cp.id
    JOIN care_plan_steps cps ON cp.id = cps.care_plan_id
    WHERE pcp.patient_id = $patient_id
    ORDER BY pcp.assigned_at DESC, cps.step_number ASC
");

?>
<style>
@media print {
    body {
        font-size: 12px;
    }

    .btn,
    .text-end,
    nav,
    footer {
        display: none !important;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table td,
    .table th {
        border: 1px solid #000 !important;
        padding: 6px !important;
    }
}
</style>

<div class="container py-4">
    <div class="mb-4 text-center">
        <h2 class="text-primary">Patient Report</h2>
        <hr>
    </div>
    <div class="text-end mb-3">

        <button onclick="window.print()" class="btn btn-primary">Print Report</button>

    </div>

    <!-- Treatments Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Treatments</h5>
        </div>
        <div class="card-body">
            <?php if ($treatments->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Treatment</th>
                            <th>Doctor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $treatments->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['treatment_date'])) ?></td>
                            <td><?= htmlspecialchars($row['treatment']) ?></td>
                            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">No treatments found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tests Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Tests</h5>
        </div>
        <div class="card-body">
            <?php if ($tests->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Test</th>
                            <th>Doctor</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $tests->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['assigned_date'])) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['remarks'])) ?></td>
                            <td>
                                <?php if ($row['status'] == 'Completed'): ?>
                                <span class="badge bg-success">Completed</span>
                                <?php else: ?>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['report_file'])): ?>
                                <a href="uploads/<?= htmlspecialchars($row['report_file']) ?>"
                                    class="btn btn-sm btn-outline-primary" target="_blank">View Report</a>
                                <?php else: ?>
                                <span class="text-muted">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">No tests found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Care Plans Section -->
    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Care Plans & Instructions</h5>
        </div>
        <div class="card-body">
            <?php if ($query->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Plan Title</th>
                            <th>Description</th>
                            <th>Assigned Date</th>
                            <th>Step #</th>
                            <th>Step Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $last_plan_id = null;
                            while ($row = $query->fetch_assoc()):
                            ?>
                        <tr>
                            <td>
                                <?php
                                        if ($last_plan_id !== $row['patient_plan_id']) {
                                            echo htmlspecialchars($row['plan_title']);
                                            $last_plan_id = $row['patient_plan_id'];
                                        }
                                        ?>
                            </td>
                            <td>
                                <?= ($last_plan_id === $row['patient_plan_id']) ? htmlspecialchars($row['plan_description']) : '' ?>
                            </td>
                            <td>
                                <?= ($last_plan_id === $row['patient_plan_id']) ? date('d M Y', strtotime($row['assigned_at'])) : '' ?>
                            </td>

                            <td><?= htmlspecialchars($row['step_number']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['step_description'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">No care plans assigned to this patient.</p>
            <?php endif; ?>
        </div>
    </div>
</div>