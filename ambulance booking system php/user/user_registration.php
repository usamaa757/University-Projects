<?php
require("../db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        $message = "Error: Email already exists.";
        $message_type = "danger";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful!";
            $message_type = "success";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            $message_type = "danger";
        }
    }

    // Close the database connection
    $conn->close();

    // Redirect to the registration page with message
    header("Location: user_registration.php?message=" . urlencode($message) . "&message_type=" . $message_type);
    exit();
}
include("../header.php");
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Registeration</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($_GET['message_type']); ?>">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                    </div>
                    <?php endif; ?>

                    <form action="user_registration.php" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>