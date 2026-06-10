<?php

include 'header.php';

include 'db_connection.php';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Encrypt the password


// Check if email already exists
$checkEmail = $conn->prepare("SELECT * FROM employees WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$result = $checkEmail->get_result();

if ($result->num_rows > 0) {
echo "<script>
alert('Email already exists. Please use another email.');
window.history.back();
</script>";
} else {
// Insert data into the database
$stmt = $conn->prepare("INSERT INTO employees (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
echo "<script>
alert('Registration successful!');
window.location.href = 'login.php';
</script>";
} else {
echo "<script>
alert('Error during registration. Please try again.');
window.history.back();
</script>";
}

$stmt->close();
}
$checkEmail->close();
}

$conn->close();
?>

<div class="container mt-5 shadow rounded border" style="max-width: 600px;">
    <h3 class="text-center">Employee Registration</h3>
    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>
        <div class="text-center m-3">

            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>
</div>
</body>

</html>