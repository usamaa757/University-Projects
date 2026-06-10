<?php
include 'header.php';
include '../config/database.php';

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO care_plans (title, description, created_by) VALUES (?, ?, 1)");
    $stmt->bind_param("ss", $title, $description);
    if ($stmt->execute()) {
        $success = "Care plan created successfully. <a href='add_care_step.php?plan_id=" . $conn->insert_id . "'>Add Steps</a>";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 border rounded p-4 shadow ">

            <h3 class="text-center">Create New Care Plan</h3>
            <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label>Title</label>
                    <input name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <button class="btn btn-success">Create Care Plan</button>
            </form>
        </div>
    </div>
</div>