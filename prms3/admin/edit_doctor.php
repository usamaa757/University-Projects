<?php
include 'header.php';
include '../config/database.php';

$id = $_GET['id'] ?? 0;
$success = $error = "";

// Fetch doctor data
$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    echo "<div class='alert alert-danger'>Doctor not found.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $department      = $_POST['department'];
    $phone  = $_POST['phone'];
    $password = $_POST['password'] ?? '';

    if (!empty($password)) {
        // If password is provided, hash and update
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, email = ?, phone = ?, department = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $department, $hashed, $id);
    } else {
        // Update without password
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, email = ?, phone = ?, department = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $department, $id);
    }

    if ($stmt->execute()) {
        $success = "Doctor updated successfully.";
        $doctor['name'] = $name;
        $doctor['email'] = $email;
        $doctor['phone'] = $phone;
        $doctor['department'] = $department;
    } else {
        $error = "Update failed.";
    }
}


?>

<div class="container mt-5">


  
    <div class="row justify-content-center ">
        <div class="col-md-6 border rounded p-4 shadow ">
            <h3 class="text-center">Edit doctor</h3>
              <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
            <form action="" method="POST">
                <form method="post">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($doctor['name']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($doctor['email']) ?>"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Department</label>
                        <input type="text" name="department" value="<?= htmlspecialchars($doctor['department']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($doctor['phone']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>New Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <button class="btn btn-primary">Update</button>
                    <a href="manage_doctor.php" class="btn btn-secondary">Cancel</a>
                </form>
        </div>
    </div>
</div>