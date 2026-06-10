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


    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");


    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        // Setting session variables for user data
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $role;
        $_SESSION['user_id'] = $user['user_id'];

        if ($role === 'admin') {

            header("Location: admin/dashboard.php");
            exit();
        } elseif ($role === 'artist') {

            header("Location: artist/dashboard.php");
            exit();
        } else {

            header("Location: customer/dashboard.php");
            exit();
        }
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
                                <option value="artist">Artist</option>
                                <option value="customer">Customer</option>
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