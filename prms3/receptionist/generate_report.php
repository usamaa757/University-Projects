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
if (isset($_POST['upload_report'])) {
    $test_id = $_POST['test_id'];
    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['report_file']['tmp_name'];
        $fileName = basename($_FILES['report_file']['name']);
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '_', $fileName); // sanitized name
        $dest = 'uploads/' . $fileName;

       if (move_uploaded_file($fileTmpPath, $dest)) {
    $update_stmt = $conn->prepare("UPDATE patient_tests SET report_file = ?, status = 'Completed' WHERE test_id = ?");
    $update_stmt->bind_param("si", $fileName, $test_id);
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: generate_report.php?patient_id=$patient_id&success=1");
    exit;
} else {
    header("Location: generate_report.php?patient_id=$patient_id&success=0");
    exit;
}

}
}

?>

<!-- Tests Section -->
<div class="container border rounded shadhow mt-5 p-3">
   
            <h3 class="text-center">Uplad Test Reports</h5>
        <?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Report uploaded successfully!
        </div>
    <?php else: ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Failed to upload the file!
        </div>
    <?php endif; ?>
<?php endif; ?>

            <?php if ($tests->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
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

                                        <?php if (empty($row['report_file'])): ?>
                                            <form method="post" enctype="multipart/form-data" style="display:inline-block;">
                                                <input type="hidden" name="test_id" value="<?= $row['test_id'] ?>">
                                                <input type="file" name="report_file" accept=".pdf,.jpg,.png,.doc,.docx" required>
                                                <button type="submit" name="upload_report" class="btn btn-sm btn-success mt-1">Upload</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="../uploads/<?= htmlspecialchars($row['report_file']) ?>" target="_blank">View Report</a>
                                        <?php endif; ?>                                <?php endif; ?>
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
