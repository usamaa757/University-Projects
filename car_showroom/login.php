<?php
include 'header.php';
include 'db.php';
session_start();

// Initialize the error message variable
$errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Setting session variables for user data
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        $_SESSION['role'] = $role;

        if ($role === 'admin') {
            $_SESSION['admin_id'] = $user['admin_id'];
            header("Location: admin/dashboard.php");
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid email or password!'); window.location.href='login.php';</script>";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-3">
                <h4 class="text-center">Login</h4>
                <div class="card-body">

                    <!-- Display error message if login failed -->
                    <?php if ($errorMsg): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errorMsg); ?>
                    </div>
                    <?php endif; ?>

                    <form method="post" class="form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Login As</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>