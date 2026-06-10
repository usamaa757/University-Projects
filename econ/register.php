<?php include 'header.php';
include 'db.php';
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Basic validation
    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email already registered.");
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $phone, $hashed);

    if ($stmt->execute()) {
        $success = "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

$conn->close();
?>


<main class="container">

    <!-- Register Section -->
    <section class="section">
        <div class="section-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create an Account</h2>
        </div>
        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <div class="text-center">

                <button class="btn" type="submit">Register</button>
            </div>
        </form>

        <p style="margin-top: 20px;">Already have an account? <a href="login.php"
                style="color: var(--dark); text-decoration: underline;">Login here</a>.</p>
    </section>

</main>
<?php include 'footer.php'; ?>

</body>

</html>