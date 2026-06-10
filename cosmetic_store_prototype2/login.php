<?php
include 'header.php';
include "db.php";
session_start();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_id = $_POST['email_id'];
    $password = $_POST['password'];

    // Check in users table
    $sql_user = "SELECT * FROM users WHERE email_id = ?";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("s", $email_id);
    $stmt->execute();
    $result_user = $stmt->get_result();

    // Check in admins table
    $sql_admin = "SELECT * FROM admins WHERE email_id = ?";
    $stmt = $conn->prepare($sql_admin);
    $stmt->bind_param("s", $email_id);
    $stmt->execute();
    $result_admin = $stmt->get_result();

    // Determine if the user is found in users or admins
    if ($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
    } elseif ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
    } else {
        echo "<script>alert('No user found with this email!');window.location.href = 'login.php';
</script>";

        exit();
    }

    // Verify password
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] =  $row['user_id'];
        $_SESSION['admin_id'] =  $row['admin_id'];
        $_SESSION['email_id'] = $email_id;
        $_SESSION['first_name'] = $row['first_name'];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid password!');window.location.href = 'login.php';
</script>";
        exit();
    }
}
?>





<div class="form-container">
    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email_id" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn-login" type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>

</html>