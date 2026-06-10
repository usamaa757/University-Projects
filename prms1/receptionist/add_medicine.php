<?php
include 'header.php';
include '../config/database.php';

$name = $type = $description = $quantity = $price = '';
$success = $error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);



    $stmt = $conn->prepare("INSERT INTO medicines (name, type, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $type, $description);
    if ($stmt->execute()) {
        $success = "Medicine added successfully.";
        $name = $type = $description = '';
        $quantity = $price = '';
    } else {
        $error = "Error adding medicine.";
    }
    $stmt->close();
}
?>


<div class="container mt-5">
    <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?> <div class="row justify-content-center">
        <div class="col-md-6 border rounded p-4 shadow ">
            <h3 class="text-center mb-3"> Add New Medicine</h3>
            <form action="" method="POST">
                <form method="POST" class="border p-4 rounded shadow-sm bg-light">
                    <div class="mb-3">
                        <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" name="type" value="<?= htmlspecialchars($type) ?>" class="form-control"
                            placeholder="Tablet, Syrup, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            rows="3"><?= htmlspecialchars($description) ?></textarea>
                    </div>


                    <button type="submit" class="btn btn-primary">Save Medicine</button>
                    <a href="medicine_list.php" class="btn btn-success">Medicine List</a>
                </form>
        </div>
    </div>
</div>