<?php

include 'header.php';

include 'db_connection.php';
// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user data from database
    $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['employee_name'] = $user['name'];

            header('Location: employee/dashboard.php');
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>


<div class="container mt-5 rounded shadow border" style="max-width: 600px;">
    <h2 class="text-center">Employee Login</h2>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="text-center m-3">

            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
</div>
</body>

</html>