<?php

include 'header.php';

include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check in users table (Job Seekers & Employers)
    $sql_user = "SELECT user_id, role, password FROM users WHERE email = ? AND status = 'active'";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $stmt_user->close();

    if ($user = $result_user->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>
            alert('Invalid Password!');
             window.location.href = 'login.php';
             </script>";
        }
    } else {
        echo "<script>
            alert('User not found!');
            window.location.href = 'login.php';
                </script>";
    }
}

?>



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
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                    <p class="text-center mt-3">
                        Don't have an account? <a href="register.php">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>