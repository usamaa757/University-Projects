<?php
include 'header.php';
include '../config/database.php';

$doctor_id = $_SESSION['user_id'] ?? null;
$patient_id = $_GET['patient_id'] ?? null;

if (!$doctor_id || !$patient_id) {
    echo "<div class='alert alert-danger'>Missing doctor or patient ID.</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_ids = $_POST['test_ids'] ?? [];
    $remarks = $_POST['remarks'] ?? '';

    if (!empty($test_ids)) {
        $stmt = $conn->prepare("INSERT INTO patient_tests (patient_id, doctor_id, test_id, remarks) VALUES (?, ?, ?, ?)");

        foreach ($test_ids as $test_id) {
            $stmt->bind_param("iiis", $patient_id, $doctor_id, $test_id, $remarks);
            $stmt->execute();
        }

        $stmt->close();
        echo "<div class='alert alert-success'>Tests assigned successfully.</div>";
    } else {
        echo "<div class='alert alert-warning'>Please select at least one test.</div>";
    }
}

// Fetch tests
$tests = $conn->query("SELECT id, name FROM tests");
?>

<div class="container mt-5 border rounded shadow p-4">
    <h3 class="text-center">Assign Tests to Patient</h3>
    <form method="POST">
        <div class="form-group">
            <label for="test_ids">Select Tests</label>
            <select name="test_ids[]" class="form-control" multiple required>
                <?php while ($row = $tests->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select multiple.</small>
        </div>
        <div class="form-group mt-2">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Assign Tests</button>
    </form>
</div>