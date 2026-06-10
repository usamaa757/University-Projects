<?php
include 'header.php';
include '../config/database.php';

$id = $_GET['id'] ?? 0;
$success = $error = "";

// Fetch receptionist data
$stmt = $conn->prepare("SELECT * FROM receptionists WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$receptionist = $result->fetch_assoc();

if (!$receptionist) {
    echo "<div class='alert alert-danger'>Receptionist not found.</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE receptionists SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    if ($stmt->execute()) {
        $success = "Receptionist updated successfully.";
        // Refresh data
        $receptionist['name'] = $name;
        $receptionist['email'] = $email;
    } else {
        $error = "Update failed.";
    }
}
?>

<div class="container mt-5">


    <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <div class="row justify-content-center ">
        <div class="col-md-6 border rounded p-4 shadow ">
            <h3 class="text-center">Edit Receptionist</h3>
            <form action="" method="POST">
                <form method="post">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($receptionist['name']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($receptionist['email']) ?>"
                            class="form-control" required>
                    </div>
                    <button class="btn btn-primary">Update</button>
                    <a href="manage_receptionist.php" class="btn btn-secondary">Cancel</a>
                </form>
        </div>