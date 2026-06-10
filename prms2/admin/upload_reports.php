<?php
include 'sidebar.php';
include '../db.php';

// Get patient_id from URL
if (isset($_GET['patient_id'])) {
    $patient_id = intval($_GET['patient_id']);


    // Fetch patient tests
    $tests = [];
    $query = "SELECT tests.test_id, tests.test_name, treatment.treatment_id
          FROM treatment_tests 
          JOIN tests ON treatment_tests.test_id = tests.test_id 
          JOIN treatment ON treatment_tests.treatment_id = treatment.treatment_id 
          WHERE treatment.patient_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tests[] = $row;
    }
} else {
    echo "<script>alert('No patient is selected.'); window.history.back();</script>";
    exit;
}

if (isset($_POST['upload_reports'])) {
    $patient_id = intval($_GET['patient_id']);

    $test_ids = $_POST['test_id'];
    $treatment_ids = $_POST['treatment_id'];
    $files = $_FILES['report_file'];

    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    $upload_dir = 'uploads/reports/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    for ($i = 0; $i < count($test_ids); $i++) {
        if (!empty($files['name'][$i])) {
            $file_ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);

            if (in_array(strtolower($file_ext), $allowed_types)) {
                $new_filename = uniqid('report_', true) . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                    $stmt = $conn->prepare("INSERT INTO test_reports (patient_id, treatment_id, test_id, file_path) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiis", $patient_id, $treatment_ids[$i], $test_ids[$i], $upload_path);
                    if ($stmt->execute()) {
                        echo "<script>alert('All reports uploaded successfully!'); window.location.href='medical_history.php?patient_id={$patient_id}';</script>";
                    } else {
                        echo "<script>alert('Error uploading reports.'); window.history.back();</script>";
                        exit;
                    }
                }
            }
        }
    }
}

?>

<div class="main-content">
    <div class="container p-3">
        <h2 class="text-center mb-4">Upload Test Reports for Patient ID: <?php echo $patient_id; ?></h2>

        <form action="upload_reports.php?patient_id=<?php echo $patient_id; ?>" method="POST"
            enctype="multipart/form-data">
            <div class="row">
                <?php if (!empty($tests)): ?>
                <?php foreach ($tests as $test): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title text-center"><?php echo htmlspecialchars($test['test_name']); ?></h5>

                            <input type="hidden" name="test_id[]" value="<?php echo $test['test_id']; ?>">
                            <input type="hidden" name="treatment_id[]" value="<?php echo $test['treatment_id']; ?>">

                            <div class="mb-3">
                                <label for="report_file_<?php echo $test['test_id']; ?>" class="form-label">Upload
                                    Report</label>
                                <input type="file" name="report_file[]" id="report_file_<?php echo $test['test_id']; ?>"
                                    class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="alert alert-info text-center">No tests found for this patient.</div>
                <?php endif; ?>
            </div>

            <?php if (!empty($tests)): ?>
            <div class="text-center mt-4">
                <button type="submit" name="upload_reports" class="btn btn-primary">Upload All Reports</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>