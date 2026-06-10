<?php
include 'sidebar.php';
include '../db.php';

$medicine_id = $_GET['medicine_id'] ?? null;
if (!$medicine_id) {
    die("Invalid request");
}

$edit_data = $conn->query("SELECT * FROM medicines WHERE medicine_id = $medicine_id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $frequency = $_POST['frequency'];

    $stmt = $conn->prepare("UPDATE medicines SET medicine_name=?, dosage=?, frequency=? WHERE medicine_id=?");
    $stmt->bind_param("sssi", $medicine_name, $dosage, $frequency, $medicine_id);
    if ($stmt->execute()) {

        echo "<script>alert('Medication updated successfully'); window.location='medicine_list.php';</script>";
    } else {
        echo "<script>alert('Failed to update medicine.'); window.history.back();</script>";
        exit;
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="container p-3 card">
        <h4>Edit Medicine</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Medication Name</label>
                <input type="text" name="medicine_name" class="form-control" required
                    value="<?= htmlspecialchars($edit_data['medicine_name']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Dosage</label>
                <input type="text" name="dosage" class="form-control" required
                    value="<?= htmlspecialchars($edit_data['dosage']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Frequency</label>
                <input type="text" name="frequency" class="form-control" required
                    value="<?= htmlspecialchars($edit_data['frequency']) ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="medicines_list.php" class="btn btn-secondary">Back to List</a>
            </div>
        </form>
    </div>
</div>