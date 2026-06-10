<?php
include '../db.php';
include 'header.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$msg = "";

// Fetch user details
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Update name and email
    $conn->query("UPDATE users SET name = '$name', email = '$email' WHERE user_id = $user_id");

    // Update password if provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id");
    }

    $msg = "User updated successfully!";

    // Refresh user data
    $result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
    $user = $result->fetch_assoc();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Edit User</h4>
                </div>
                <div class="card-body">
                    <?php if ($msg): ?>
                        <p class="text-success text-center"><?= $msg ?></p>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Name:</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password:</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Leave blank to keep old password">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="manage_users.php" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
