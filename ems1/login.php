<?php
session_start();
include 'header.php';
include 'db.php';

$errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Check organizers table
    $stmt = $conn->prepare("SELECT * FROM organizers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $org_result = $stmt->get_result();

    if ($org_result->num_rows === 1) {
        $org = $org_result->fetch_assoc();
        if (password_verify($password, $org['password'])) {
            $_SESSION['user_id'] = $org['organizer_id'];
            $_SESSION['username'] = $org['username'];
            $_SESSION['role'] = 'organizer';
            header("Location: organizer/dashboard.php");
            exit;
        }
    }

    // Check attendees table
    $stmt = $conn->prepare("SELECT * FROM attendees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $att_result = $stmt->get_result();

    if ($att_result->num_rows === 1) {
        $att = $att_result->fetch_assoc();
        if (password_verify($password, $att['password'])) {
            $_SESSION['user_id'] = $att['attendee_id'];
            $_SESSION['username'] = $att['username'];
            $_SESSION['role'] = 'attendee';
            header("Location: attendee/dashboard.php");
            exit;
        }
    }

    // Show error
    $errorMsg = "Invalid email or password.";
    $stmt->close();
    $conn->close();
}
?>

<!-- Styles -->
<style>
.login-container {
    max-width: 400px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.login-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2c3e50;
}

.login-container label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.login-container input[type="email"],
.login-container input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    transition: border-color 0.3s ease;
}

.login-container input:focus {
    border-color: #3498db;
    outline: none;
}

.login-container button {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.login-container button:hover {
    background-color: #2980b9;
}

.message {
    text-align: center;
    margin-bottom: 20px;
    padding: 10px;
    color: #e74c3c;
    background-color: #fdecea;
    border: 1px solid #e0b4b4;
    border-radius: 8px;
}
</style>

<!-- Login Form -->
<div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($errorMsg)): ?>
    <div class="message"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>

</html>