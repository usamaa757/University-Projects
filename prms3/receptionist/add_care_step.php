<?php
include 'header.php';
include '../config/database.php';

$plan_id = $_GET['plan_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step_number = $_POST['step_number'];
    $step_description = $_POST['step_description'];

    $stmt = $conn->prepare("INSERT INTO care_plan_steps (care_plan_id, step_number, step_description, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $plan_id, $step_number, $step_description);
    $stmt->execute();
}

$steps = $conn->query("SELECT * FROM care_plan_steps WHERE care_plan_id = $plan_id ORDER BY step_number");

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10 border rounded p-4 shadow ">

            <h3 class="text-center">Add Steps to Care Plan</h3>
            <form method="post" class="mb-4 border p-3">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label>Step Number</label>
                        <input name="step_number" type="number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label>Step Description</label>
                        <textarea name="step_description" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-primary">Add Step</button>
                    </div>
                </div>
            </form>

            <h5 class="text-center">Existing Steps</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Step Number</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($step = $steps->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($step['step_number']) ?></td>
                        <td><?= nl2br(htmlspecialchars($step['step_description'])) ?></td>
                        <td><?= htmlspecialchars($step['created_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>