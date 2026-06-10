<?php
include 'header.php';
include "db.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($role === 'admin') {
            $_SESSION['admin_id'] =  $user['admin_id'];
        } else {

            $_SESSION['user_id'] =  $user['user_id'];
        }
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        $_SESSION['role'] = $role;

        if ($role === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        echo "<script>
            alert('Invalid email or password!');
            window.location.href = 'login.php';
        </script>";
    }
}
?>


<!-- HTML Login Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Login As</label>
                            <select name="role" class="form-control" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn btn-dark">Login</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">
                        Don't have an account? <a href="register.php">Register</a><br>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>