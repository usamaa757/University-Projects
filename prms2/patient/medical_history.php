<?php
include 'sidebar.php';
include '../db.php';

$patient_id = $_SESSION['patient_id'];


$all_treatments = [];

$stmt = $conn->prepare("SELECT t.treatment_id, t.treatment_date, p.disease 
        FROM treatment t
        INNER JOIN patients p ON t.patient_id = p.patient_id
        WHERE t.patient_id = ?
        ORDER BY t.treatment_date DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $treatment_id = $row['treatment_id'];
    $treatment_date = $row['treatment_date'];
    $disease = $row['disease'];

    // Fetch tests and reports for this treatment
    $tests = [];
    $test_stmt = $conn->prepare("SELECT t.test_id, t.test_name 
            FROM treatment_tests tt 
            JOIN tests t ON tt.test_id = t.test_id 
            WHERE tt.treatment_id = ?");
    $test_stmt->bind_param("i", $treatment_id);
    $test_stmt->execute();
    $test_res = $test_stmt->get_result();

    while ($test = $test_res->fetch_assoc()) {
        $test_id = $test['test_id'];
        $test_name = $test['test_name'];

        // Fetch report for this test
        $report_stmt = $conn->prepare("SELECT file_path FROM test_reports WHERE treatment_id = ? AND test_id = ?");
        $report_stmt->bind_param("ii", $treatment_id, $test_id);
        $report_stmt->execute();
        $report_res = $report_stmt->get_result();
        $report = $report_res->fetch_assoc();

        $tests[] = [
            'test_name' => $test_name,
            'report_file' => $report ? $report['file_path'] : null
        ];
    }

    // Fetch medicines for this treatment
    $medicines = [];
    $med_stmt = $conn->prepare("SELECT m.* 
            FROM treatment_medicines tm 
            JOIN medicines m ON tm.medicine_id = m.medicine_id 
            WHERE tm.treatment_id = ?");
    $med_stmt->bind_param("i", $treatment_id);
    $med_stmt->execute();
    $med_res = $med_stmt->get_result();
    while ($med = $med_res->fetch_assoc()) {
        $medicines[] = $med;
    }


    // Add everything to array
    $all_treatments[] = [
        'treatment_id' => $treatment_id,
        'treatment_date' => $treatment_date,
        'patient_id' => $patient_id,
        'disease' => $disease,
        'tests' => $tests,
        'medicines' => $medicines
    ];
}
?>


<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar included above -->
        </div>

        <div class="col-md-9">
            <div class="card rounded-3 border shadow p-3">

                <div class="text-center mb-4">
                    <h3>Patient Treatment Summary</h3>
                </div>

                <?php if (!empty($all_treatments)): ?>
                <?php foreach ($all_treatments as $treatment): ?>
                <div class="row g-4 mb-4 border-bottom pb-3">
                    <!-- Treatment Info -->
                    <div class="p-2">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h5 class="text-primary">Treatment Info</h5>
                            <p class="mb-1"><strong>Treatment ID:</strong>
                                <?= htmlspecialchars($treatment['treatment_id']); ?>
                            </p>
                            <p class="mb-1"><strong>Disease:</strong> <?= htmlspecialchars($treatment['disease']); ?>
                            </p>
                            <p class="mb-1"><strong>Date:</strong>
                                <?= date('d M, Y', strtotime($treatment['treatment_date'])); ?></p>
                            <p class="mb-0"><strong>Patient ID:</strong>
                                <?= htmlspecialchars($treatment['patient_id']); ?></p>

                            <a href="add_instruction.php?patient_id=<?= $patient_id ?>&treatment_id=<?= $treatment_id ?>"
                                class="btn btn-success my-2">Add Instructions</a>
                        </div>
                    </div>

                    <!-- Tests -->
                    <div class="p-2s">

                        <div class="border rounded p-3 h-100 bg-light">
                            <h5 class="text-info">Tests</h5>

                            <?php if (!empty($treatment['tests'])): ?>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <?php foreach ($treatment['tests'] as $test): ?>
                                <div class="border rounded p-2 bg-white" style="min-width: 140px;">
                                    <span class="badge bg-info text-dark mb-2"><?= $test['test_name']; ?></span><br>
                                    <?php if (!empty($test['report_file'])): ?>
                                    <a href="<?= $test['report_file']; ?>" target="_blank"
                                        class="btn btn-sm btn-outline-primary w-100">
                                        View Report
                                    </a>
                                    <?php else: ?>
                                    <p class="text-muted small mb-0">No report</p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (empty($test['report_file'])): ?>
                            <div class="mt-2">
                                <a href="upload_reports.php?patient_id=<?= htmlspecialchars($all_treatments[0]['patient_id']); ?>"
                                    class="btn btn-primary">
                                    Upload Reports
                                </a>

                            </div>
                            <?php endif; ?>
                            <?php else: ?>
                            <p class="text-muted mb-0">No tests</p>
                            <?php endif; ?>
                        </div>

                    </div>


                    <!-- Medicines -->
                    <div class="">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h5 class="text-success">Medicines</h5>
                            <?php if (!empty($treatment['medicines'])): ?>
                            <?php foreach ($treatment['medicines'] as $medicine): ?>
                            <div class="mb-2">
                                <span
                                    class="badge bg-success"><?= htmlspecialchars($medicine['medicine_name']) ?></span>
                                <small class="text-muted">
                                    (<?= htmlspecialchars($medicine['dosage']) ?>,
                                    <?= htmlspecialchars($medicine['frequency']) ?>)
                                </small>
                            </div>
                            <?php endforeach; ?>

                            <?php else: ?>
                            <p class="text-muted mb-0">No medicines</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="alert alert-warning text-center">
                    No treatment history found for this patient.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>

</html>