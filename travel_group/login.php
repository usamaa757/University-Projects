<?php
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {

        // First, try users table
        $query_user = "SELECT * FROM users WHERE email = '$email'";
        $result_user = mysqli_query($conn, $query_user);

        if ($result_user && mysqli_num_rows($result_user) === 1) {
            $row = mysqli_fetch_assoc($result_user);

            if (password_verify($password, $row['password_hash'])) {
                // User login successful
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = 'user';

                header("Location: user/dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
                exit;
            }
        }

        // Now try admin table
        $query_admin = "SELECT * FROM admin WHERE email = '$email'";
        $result_admin = mysqli_query($conn, $query_admin);

        if ($result_admin && mysqli_num_rows($result_admin) === 1) {
            $row = mysqli_fetch_assoc($result_admin);

            if (password_verify($password, $row['password_hash'])) {
                // Admin login successful
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = 'admin';

                header("Location: admin/dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
                exit;
            }
        }

        // If email not found in either table
        echo "<script>alert('Email not found. Please register or contact admin.'); window.location.href='register.php';</script>";
        exit;
    } else {
        echo "<script>alert('Please enter both email and password.'); window.location.href='login.php';</script>";
        exit;
    }

    mysqli_close($conn);
}
?>




<div class="form-container">


    <form method="post" class="forms">
        <h2>Login</h2>

        <label>Email Address</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <div class="text-center">

            <button class="btn" type="submit">Login</button>
        </div>
    </form>
</div>
</body>

</html>