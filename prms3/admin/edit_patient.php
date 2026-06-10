<?php
include 'header.php';
include '../config/database.php';

$id = $_GET['id'] ?? 0;
$success = $error = "";

// Fetch patient data
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "<div class='alert alert-danger'>Patient not found.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $age      = $_POST['age'];
    $disease  = $_POST['disease'];
    $password = $_POST['password'] ?? '';

    if (!empty($password)) {
        // If password is provided, hash and update
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE patients SET name = ?, email = ?, age = ?, disease = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssissi", $name, $email, $age, $disease, $hashed, $id);
    } else {
        // Update without password
        $stmt = $conn->prepare("UPDATE patients SET name = ?, email = ?, age = ?, disease = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $name, $email, $age, $disease, $id);
    }

    if ($stmt->execute()) {
        $success = "Patient updated successfully.";
        $patient['name'] = $name;
        $patient['email'] = $email;
        $patient['age'] = $age;
        $patient['disease'] = $disease;
    } else {
        $error = "Update failed.";
    }
}


?>

<div class="container mt-5">


    <div class="row justify-content-center ">
        <div class="col-md-6 border rounded p-4 shadow ">
            <h3 class="text-center">Edit Patient</h3>
            
    <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
            <form action="" method="POST">
                <form method="post">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($patient['name']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Age</label>
                        <input type="text" name="age" value="<?= htmlspecialchars($patient['age']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Disease</label>
                        <input type="disease" name="disease" value="<?= htmlspecialchars($patient['disease']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>New Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <button class="btn btn-primary">Update</button>
                    <a href="manage_patient.php" class="btn btn-secondary">Cancel</a>
                </form>
        </div>
    </div>
</div>