<?php

session_start();
include("../db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session and set user data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            // Set success message and redirect
            
            header("Location: user_dashboard.php");
            exit();
        } else {
            // Set error message
            $_GET['error'] = "Incorrect password.";
        }
    } else {
        // Set error message
        $_GET['error'] = "Email not registered.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

require("../header.php");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Login</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_GET['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                   

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="class-center text-center">
                        <button type="submit" class="btn btn-primary">Login</button>
                        </div>
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
