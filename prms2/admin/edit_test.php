<?php
include 'sidebar.php';
include '../db.php';

$test_id = $_GET['test_id'] ?? null;
if (!$test_id) {
    die("Invalid request");
}

$edit_data = $conn->query("SELECT * FROM tests WHERE test_id = $test_id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $test_name = $_POST['test_name'];
    $dosage = $_POST['dosage'];
    $frequency = $_POST['frequency'];

    $stmt = $conn->prepare("UPDATE tests SET test_name=? WHERE test_id=?");
    $stmt->bind_param("si", $test_name, $test_id);
    if ($stmt->execute()) {

        echo "<script>alert('Test updated successfully'); window.location='test_list.php';</script>";
    } else {
        echo "<script>alert('Failed to update test.'); window.history.back();</script>";
        exit;
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="container p-3 card">
        <h4>Edit Test</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Medication Name</label>
                <input type="text" name="test_name" class="form-control" required
                    value="<?= htmlspecialchars($edit_data['test_name']) ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="tests_list.php" class="btn btn-secondary">Back to List</a>
            </div>
        </form>
    </div>
</div>