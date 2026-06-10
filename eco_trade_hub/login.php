<?php
include("header.php");
include("db_connection.php");
session_start();

$msg = '';
if (isset($_GET['msg'])) {
    $msg = urldecode($_GET['msg']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Check if the user exists in the admin table
    $admin_result = $conn->query("SELECT * FROM admin WHERE email='$email'");
    if ($admin_result->num_rows > 0) {
        $admin = $admin_result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name'];

            // Redirect to admin dashboard
            header("Location: " . BASE_PATH . "/admin/admin_dashboard.php");
            exit();
        } else {
            header("Location: " . BASE_PATH . "/login.php?msg=" . urlencode("Invalid password."));
            exit();
        }
    }

    // Check if the user exists in the sellers table
    $seller_result = $conn->query("SELECT * FROM sellers WHERE email='$email' AND status = 'approved'");
    if ($seller_result->num_rows > 0) {
        $seller = $seller_result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $seller['password'])) {
            // Set session variables
            $_SESSION['seller_id'] = $seller['seller_id'];
            $_SESSION['email'] = $seller['email'];
            $_SESSION['user_name'] = $seller['seller_name'];
            $_SESSION['user_role'] = 'seller';

            // Redirect to seller dashboard
            header("Location: " . BASE_PATH . "/seller/seller_dashboard.php");
            exit();
        } else {
            header("Location: " . BASE_PATH . "/login.php?msg=" . urlencode("Invalid password."));
            exit();
        }
    }

    // Check if the user exists in the buyers table
    $buyer_result = $conn->query("SELECT * FROM buyers WHERE email='$email' AND status = 'approved'");
    if ($buyer_result->num_rows > 0) {
        $buyer = $buyer_result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $buyer['password'])) {
            // Set session variables
            $_SESSION['buyer_id'] = $buyer['buyer_id'];
            $_SESSION['email'] = $buyer['email'];
            $_SESSION['user_name'] = $buyer['buyer_name'];
            $_SESSION['user_role'] = 'buyer';

            // Redirect to buyer products list
            header("Location: " . BASE_PATH . "/buyer/products_list.php");
            exit();
        } else {
            header("Location: " . BASE_PATH . "/login.php?msg=" . urlencode("Invalid password."));
            exit();
        }
    }

    // If email not found in any table
    header("Location: " . BASE_PATH . "/login.php?msg=" . urlencode("Email not found or Inactive user."));
    exit();
}
?>

<!-- Login Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h2 class="text-center heading-bg bg-dark text-white p-2">Login</h2>
                <div class="p-4">
                    <?php if ($msg != ''): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($msg); ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
