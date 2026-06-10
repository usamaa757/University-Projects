<?php
// Include the header file
include 'header.php';

// Include the database connection file
include 'db_connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate form input
    if ($password !== $confirm_password) {
        $_SESSION['error_msg'] = "Passwords do not match. Please try again.";
        header("Location: register_seller.php");
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert seller into the database
    $query = "INSERT INTO sellers (seller_name, email, contact_number, password) VALUES ('$name', '$email', '$contact', '$hashed_password')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_msg'] = "Registration successful! Please log in.";
        header("Location: register_seller.php");
        exit();
    } else {
        $_SESSION['error_msg'] = "Error during registration. Please try again.";
        header("Location: register_seller.php");
        exit();
    }
}

// Close the database connection
mysqli_close($conn);
?>

<style>
    .register-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .register-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .register-container .alert {
        margin-bottom: 20px;
    }
</style>

<div class="container register-container">
    <h2>Seller Registration</h2>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_msg'];
            unset($_SESSION['success_msg']); ?>
        </div>
    <?php elseif (isset($_SESSION['error_msg'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_msg'];
            unset($_SESSION['error_msg']); ?>
        </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form action="register_seller.php" method="POST">
        <!-- Name -->
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Contact -->
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="text" class="form-control" id="contact" name="contact" required>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn text-white bg-primary btn-block">Register</button>
    </form>

    <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>

</html>