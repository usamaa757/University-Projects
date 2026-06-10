<?php
include 'header.php';
include '../config/database.php';

$care_plans = $conn->query("SELECT id, title FROM care_plans");
$patients = $conn->query("SELECT id, name, disease FROM patients");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $care_plan_id = $_POST['care_plan_id'];

    $stmt = $conn->prepare("INSERT INTO patient_care_plans (patient_id, care_plan_id, assigned_by) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $patient_id, $care_plan_id);
    $stmt->execute();
    echo "<div class='alert alert-success'>Care plan assigned!</div>";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 border rounded p-4 shadow ">

            <h3 class="text-center">Assign Care Plan to Patient</h3>
            <form method="post">
                <div class="mb-3">
                    <label>Select Patient</label>
                    <select name="patient_id" class="form-control" required>
                        <?php while ($row = $patients->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?> - <?= $row['disease'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Select Care Plan</label>
                    <select name="care_plan_id" class="form-control" required>
                        <?php while ($row = $care_plans->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button class="btn btn-primary">Assign</button>
            </form>
        </div>