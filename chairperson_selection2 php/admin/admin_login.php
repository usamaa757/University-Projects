<?php
session_start();
include '../db_connection.php'; // Update the path if necessary

$errorMsg ="";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_POST['admin_id'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Password is correct, start the session
            $_SESSION['admin_id'] = $admin_id;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $errorMsg = "Invalid admin ID or password.";
        }
    } else {
        $errorMsg = "Invalid admin ID or password.";
    }

    $stmt->close();
    $conn->close();
}
require "../header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title text-center mb-0">Admin Login</h4>
                </div>
                <div class="card-body">
                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
                    <?php endif; ?>
                    <form action="admin_login.php" method="post">
                        <div class="form-group">
                            <label for="admin_id">Admin ID</label>
                            <input type="text" class="form-control" id="admin_id" name="admin_id" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>