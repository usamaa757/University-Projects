<?php
include 'header.php';
include 'db_connect.php';


$message = '';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // Check in agents table (must be approved)
    $agent_sql = "SELECT * FROM agents WHERE email='$email' AND password='$password' AND status='Approved'";
    $agent_result = $conn->query($agent_sql);

    // Check in users table
    $user_sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $user_result = $conn->query($user_sql);

    if ($agent_result->num_rows > 0) {
        $agent = mysqli_fetch_assoc($agent_result);

        $_SESSION['role'] = "agent";
        $_SESSION['agent_id'] = $agent['id'];

        header("Location: agent_dashboard.php");
        exit();
    } elseif ($user_result->num_rows > 0) {
        $user = mysqli_fetch_assoc($user_result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_address'] = $user['address'];
        $_SESSION['role'] = $user['role'];


        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $message = "Invalid credentials or account not approved yet.";
    }
}
?>

<div class="container">

    <h2>🌾 Unified Login Portal</h2>

    <form method="POST" class="register-form">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit" name="login" class="btn">Login</button>
    </form>

    <p style="color:red; text-align:center;"><?php echo $message; ?></p>

    <p style="text-align:center;">
        New User? <a href="user_register.php">Register as User</a><br>
        Register as Agent? <a href="agent_register.php">Click here</a>
    </p>
</div>
</body>

</html>