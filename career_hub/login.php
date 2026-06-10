<?php

include 'header.php';

include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check in users table (Job Seekers & Employers)
    $sql_user = "SELECT * FROM users WHERE email = ? AND status = 'active'";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $stmt_user->close();

    if ($user = $result_user->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            }
            if ($user['role'] == 'employer') {
                header("Location: employer/dashboard.php");
                exit();
            }
            if ($user['role'] == 'job_seeker') {
                header("Location: job_seeker/dashboard.php");
                exit();
            } else {
                echo "<script>
                alert('Invalid user role!');
                 window.location.href = 'login.php';
                 </script>";
            }
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
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h3>Login</h3>
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
                        <div class="text-center">

                            <button type="submit" class="btn btn-outline-dark">Login</button>
                        </div>
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