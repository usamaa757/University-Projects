<?php
include 'sidebar.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $frequency = $_POST['frequency'];
    $duration = $_POST['duration'];

    $stmt = $conn->prepare("INSERT INTO medicines (medicine_name, dosage, frequency) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $medicine_name, $dosage, $frequency);
    if ($stmt->execute()) {

        echo "<script>alert('Medication added successfully'); window.location='medicine_list.php';</script>";
    } else {
        echo "<script>alert('Failed to add medicine.'); window.history.back();</script>";
        exit;
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="container p-3 card">
        <h4 class="mb-3 text-center">Add Medicine</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Medication Name</label>
                <input type="text" name="medicine_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Dosage</label>
                <input type="text" name="dosage" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Frequency</label>
                <input type="text" name="frequency" class="form-control" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="medicine_list.php" class="btn btn-secondary">Back to List</a>
            </div>

        </form>
    </div>
</div>