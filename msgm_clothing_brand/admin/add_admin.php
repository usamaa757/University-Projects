<?php
// Include the header file
include 'header.php';

// Include the database connection file
include '../db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic form validation
    if ($password !== $confirm_password) {
        $_SESSION['error_msg'] = 'Passwords do not match. Please try again.';
        header("Location: add_admin.php");
        exit();
    }

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $query = "INSERT INTO admins (admin_name, password, email, phone) 
              VALUES ('$name', '$hashed_password', '$email', '$phone')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_msg'] = 'Registration successful! Please log in.';
        header("Location: add_admin.php");
        exit();
    } else {
        $_SESSION['error_msg'] = 'There was an error during registration. Please try again later.';
        header("Location: add_admin.php");
        exit();
    }
}

// Close database connection
mysqli_close($conn);
?>


</style>

<div class="container round shadow border mt-5 p-3" style="max-width:500px;">
    <h3>Add New Admin</h3>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_msg']; ?>
        <?php unset($_SESSION['success_msg']); ?>
    </div>
    <?php elseif (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_msg']; ?>
        <?php unset($_SESSION['error_msg']); ?>
    </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form action="add_admin.php" method="POST">
        <!-- name -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <!-- Phone -->
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" class="form-control" id="phone" name="phone" maxlength="11" required>
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

        <div class="text-center">

            <button type="submit" class="btn bg-primary text-white">Add</button>
        </div>
    </form>
</div>