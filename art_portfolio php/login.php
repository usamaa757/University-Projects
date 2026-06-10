<?php
include 'db.php';
include 'header.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Query to fetch user by email
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            $user_id = $row['user_id'];
            $name = $row['name'];
            $fetched_email = $row['email'];
            $hashed_password = $row['password_hash'];
            $fetch_role = $row['role'];

            // Verify password

            if (password_verify($password, $hashed_password)) {
                // Successful login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $fetched_email;
                $_SESSION['role'] = $fetch_role;

                if ($fetch_role === 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($fetch_role === 'artist') {
                    header("Location: artist/dashboard.php");
                } elseif ($fetch_role === 'user') {
                    header("Location: user/dashboard.php");
                } else {
                    echo "<script>alert('Unknown role detected.'); window.location.href='login.php';</script>";
                }
                exit;
            } else {
                echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Email not found. Please register first.'); window.location.href='register.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please enter both email and password.'); window.location.href='login.php';</script>";
        exit;
    }
}
?>

<!-- Login Form UI -->
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