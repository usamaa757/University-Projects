<?php
include 'header.php';
include '../config/database.php';

$patient_id = $_SESSION['user_id'];
$patient_stmt = $conn->prepare("SELECT id, name FROM patients WHERE id = ?");
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient = $patient_result->fetch_assoc();
$patient_stmt->close();



// Handle date filter
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

// Escape date for SQL
$filter_condition = '';
if (!empty($filter_date)) {
    $filter_date_escaped = $conn->real_escape_string($filter_date);
    $filter_condition = "AND DATE(t.treatment_date) = '$filter_date_escaped'";
}

// Fetch treatments
$treatments = $conn->query("
    SELECT t.treatment_date, t.treatment, GROUP_CONCAT(DISTINCT m.name ORDER BY m.name) AS medicine_names, d.name AS doctor_name
    FROM treatments t
    JOIN medicines m ON t.medicine_id = m.id
    JOIN doctors d ON t.doctor_id = d.id
    WHERE t.patient_id = $patient_id
    $filter_condition
    GROUP BY t.treatment_date, t.treatment, d.name
    ORDER BY t.treatment_date DESC
");

// Fetch tests
$test_condition = !empty($filter_date) ? "AND DATE(pt.assigned_date) = '$filter_date_escaped'" : '';
$tests = $conn->query("
    SELECT pt.*, t.name, d.name AS doctor_name
    FROM patient_tests pt
    JOIN tests t ON pt.test_id = t.id
    JOIN doctors d ON pt.doctor_id = d.id
    WHERE pt.patient_id = $patient_id
    $test_condition
    ORDER BY pt.assigned_date DESC
");

// Fetch Care Plans
$care_plan_condition = !empty($filter_date) ? "AND DATE(pcp.assigned_at) = '$filter_date_escaped'" : '';
$care_plans = $conn->query("
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
    $care_plan_condition
    ORDER BY pcp.assigned_at DESC, cps.step_number ASC
");

// Fetch instructions
$instruction_condition = !empty($filter_date) ? "AND DATE(created_at) = '$filter_date_escaped'" : '';
$instruction_query = $conn->query("
    SELECT * FROM patient_instructions 
    WHERE patient_id = $patient_id
    $instruction_condition
    ORDER BY created_at DESC
");
?>

<style>
@media print {
    body { font-size: 12px; }
    .btn, .text-end, nav, footer, form { display: none !important; }
    .table { width: 100%; border-collapse: collapse; }
    .table td, .table th { border: 1px solid #000 !important; padding: 6px !important; }
}
</style>

<div class="container py-4">
    <div class="mb-4 text-center">
        <h2 class="text-primary"> Medical Report</h2>
        <hr>
            <h3 class="text-primary">Patient <?= htmlspecialchars($patient['name']) ?></h3>
        <hr>
    </div>

<form class="mb-3 d-flex justify-content-end" method="get">
    <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient_id) ?>">
    <div class="d-flex gap-2 align-items-center">
        <label for="filter_date">Select Date:</label>
        <input type="date" name="filter_date" id="filter_date" class="form-control"
               value="<?= htmlspecialchars($filter_date) ?>" style="max-width: 200px;">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="?patient_id=<?= htmlspecialchars($patient_id) ?>" class="btn btn-outline-danger">Clear</a>
        <button type="button" onclick="window.print()" class="btn btn-primary">Print</button>
    </div>
</form>
    <!-- Treatments Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white"><h5 class="mb-0">Treatments</h5></div>
        <div class="card-body">
            <?php if ($treatments->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Treatment</th>
                            <th>Medicines</th>
                            <th>Doctor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $treatments->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['treatment_date'])) ?></td>
                            <td><?= htmlspecialchars($row['treatment']) ?></td>
                            <td><?= htmlspecialchars($row['medicine_names']) ?></td>
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
        <div class="card-header bg-success text-white"><h5 class="mb-0">Tests</h5></div>
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
                                <a href="../receptionist/uploads/<?= htmlspecialchars($row['report_file']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">View Report</a>
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
        <div class="card-header bg-secondary text-white"><h5 class="mb-0">Care Plans</h5></div>
        <div class="card-body">
            <?php if ($care_plans->num_rows > 0): ?>
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
                        <?php $last_plan_id = null; ?>
                        <?php while ($row = $care_plans->fetch_assoc()): ?>
                        <tr>
                            <td><?= ($last_plan_id !== $row['patient_plan_id']) ? htmlspecialchars($row['plan_title']) : '' ?></td>
                            <td><?= ($last_plan_id !== $row['patient_plan_id']) ? htmlspecialchars($row['plan_description']) : '' ?></td>
                            <td><?= ($last_plan_id !== $row['patient_plan_id']) ? date('d M Y', strtotime($row['assigned_at'])) : '' ?></td>
                            <td><?= htmlspecialchars($row['step_number']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['step_description'])) ?></td>
                        </tr>
                        <?php $last_plan_id = $row['patient_plan_id']; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">No care plans assigned to this patient.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Instruction Section -->
    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-secondary text-white"><h5 class="mb-0">Pre & Post Instructions</h5></div>
        <div class="card-body">
            <?php if ($instruction_query && $instruction_query->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Instruction Type</th>
                            <th>Instruction</th>
                            <th>Date Given</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $instruction_query->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['instruction_type']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['instruction_text'])) ?></td>
                            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">No care instructions found for this patient.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
