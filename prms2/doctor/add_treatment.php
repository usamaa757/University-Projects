<?php
include 'sidebar.php';
require '../db.php';

$doctor_id = $_SESSION['doctor_id'] ?? 1; // Simulate doctor ID for now if not in session
$patient_id = $_GET['patient_id'] ?? null;

$msg = '';

// Handle Treatment Suggestion Submission
if (isset($_POST['submit_treatment'])) {
    $patient_id = $_POST['patient_id'];

    $suggested_treatment = $_POST['treatment'];
    $selected_meds = $_POST['medicine_names'];

    $stmt = $conn->prepare("INSERT INTO treatment (patient_id, doctor_id, suggested_treatment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $patient_id, $doctor_id, $suggested_treatment);
    if ($stmt->execute()) {
        $treatment_id = $stmt->insert_id;

        $med_query = $conn->prepare("INSERT INTO treatment_medicines (treatment_id, medicine_id) VALUES (?, ?)");
        foreach ($selected_meds as $med_name) {
            $med_stmt = $conn->prepare("SELECT medicine_id FROM medicines WHERE medicine_name = ?");
            $med_stmt->bind_param("s", $med_name);
            $med_stmt->execute();
            $med_result = $med_stmt->get_result();
            if ($med_row = $med_result->fetch_assoc()) {
                $med_id = $med_row['medicine_id'];
                $med_query->bind_param("ii", $treatment_id, $med_id);
                $med_query->execute();
            }
        }
        echo "<script>alert('Treatment suggested successfully!'); window.location.href='add_treatment.php?patient_id='$patient_id;</script>";
    } else {
        echo "<script>alert('No treament suggested.'); window.history.back();</script>";
        exit;
    }
}

// Handle Test Suggestion
if (isset($_POST['submit_test'])) {
    $patient_id = $_POST['patient_id'];
    $selected_tests = $_POST['test_name'];

    $res = $conn->query("SELECT treatment_id FROM treatment WHERE patient_id = $patient_id");
    if ($row = $res->fetch_assoc()) {
        $treatment_id = $row['treatment_id'];

        $test_query = $conn->prepare("INSERT INTO treatment_tests (treatment_id, test_id) VALUES (?, ?)");
        foreach ($selected_tests as $test_name) {
            $test_stmt = $conn->prepare("SELECT test_id FROM tests WHERE test_name = ?");
            $test_stmt->bind_param("s", $test_name);
            $test_stmt->execute();
            $test_result = $test_stmt->get_result();
            if ($test_row = $test_result->fetch_assoc()) {
                $test_id = $test_row['test_id'];
                $test_query->bind_param("ii", $treatment_id, $test_id);
                $test_query->execute();
            }
        }


        echo "<script>alert('Test suggested successfully!'); window.location.href='add_treatment.php?patient_id='$patient_id';</script>";
    } else {
        echo "<script>alert('No test suggested.'); window.history.back();</script>";
        exit;
    }
}

// Fetch selected patient
$patient_stmt = $conn->prepare("
    SELECT p.patient_id, p.name, p.age, p.gender, p.contact, p.disease, p.address, a.status
    FROM patients p
    JOIN appointments a ON p.patient_id = a.patient_id
    WHERE a.doctor_id = ? AND p.patient_id = ? AND a.status = 'Scheduled'
    ORDER BY p.patient_id DESC
");
$patient_stmt->bind_param("ii", $doctor_id, $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient = $patient_result->fetch_assoc();

$tests_result = $conn->query("SELECT * FROM tests");
$tests = [];
while ($test = $tests_result->fetch_assoc()) {
    $tests[] = $test;
}

$medicines_result = $conn->query("SELECT * FROM medicines");
$medicines = [];
while ($med = $medicines_result->fetch_assoc()) {
    $medicines[] = $med;
}
?>
<div class="container-fluid mt-5">
    <div class="row">
        <!-- Sidebar is assumed to be fixed on the left -->
        <div class="col-md-3">
        </div>

        <!-- Main content area -->
        <div class="col-md-9">
            <h2 class="mb-4 text-center">Suggest Treatment/Test for Patient</h2>


            <!-- Patient Details -->
            <?php if ($patient): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($patient['name']) ?></h5>
                    <p><strong>Age:</strong> <?= $patient['age'] ?></p>
                    <p><strong>Gender:</strong> <?= $patient['gender'] ?></p>
                    <p><strong>Disease:</strong> <?= $patient['disease'] ?></p>
                    <p><strong>Contact:</strong> <?= $patient['contact'] ?></p>
                    <p><strong>Address:</strong> <?= $patient['address'] ?></p>
                </div>
            </div>


            <!-- Suggest Treatment & Medicines -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Suggest Treatment & Medicines</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="mb-3">
                        <input type="hidden" name="patient_id" value="<?= $patient['patient_id'] ?>">
                        <div class="mb-3">
                            <select name="medicine_names[]" class="form-select select-medicine" multiple="multiple"
                                required>
                                <?php foreach ($medicines as $med): ?>
                                <option value="<?= htmlspecialchars($med['medicine_name']) ?>">
                                    <?= htmlspecialchars($med['medicine_name']) . ' (' . htmlspecialchars($med['dosage']) . ')' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="treatment" class="form-control" placeholder="Enter treatment..."
                                required>
                        </div>
                        <button type="submit" name="submit_treatment" class="btn btn-success">Suggest</button>
                    </form>
                </div>
            </div>

            <!-- Suggest Tests Form -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Suggest Tests</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="patient_id" value="<?= $patient['patient_id'] ?>">
                        <div class="mb-3">
                            <select name="test_name[]" class="form-select select-test" multiple="multiple" required>
                                <?php foreach ($tests as $test): ?>
                                <option value="<?= htmlspecialchars($test['test_name']) ?>">
                                    <?= htmlspecialchars($test['test_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="submit_test" class="btn btn-info">Suggest</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning text-center">
                Patient not found or already treated.
            </div>
            <?php endif; ?>

            <!-- Select2 Initialization Script -->
            <script>
            $(document).ready(function() {
                $('.select-medicine, .select-test').select2({
                    placeholder: "Select options",
                    allowClear: true,
                    width: '100%'
                });
            });
            </script>

        </div>
    </div>
</div>

</body>

</html>