<?php
include 'header.php';
include '../config/database.php';

$patient_id = $_SESSION['user_id'] ?? null;

if (!$patient_id) {
    echo "<div class='alert alert-danger'>You must be logged in.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $relative_name = $_POST['relative_name'];
    $relation = $_POST['relation'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO family_history (patient_id, relative_name, relation, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $patient_id, $relative_name, $relation, $status);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Family history added.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding record.</div>";
    }

    $stmt->close();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 border rounded p-4 shadow ">
            <h2 class="text-center mb-4">Family History</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Relative's Name</label>
                    <input type="text" name="relative_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Relation (e.g., Father, Mother)</label>
                    <input type="text" name="relation" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Medical Condition</label>
                    <textarea name="status" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Add Record</button>
            </form>
        </div>
    </div>
</div>