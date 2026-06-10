<?php

include("header.php");
include("../db_connection.php");



$admin_id = $_SESSION['admin_id'];
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = $conn->real_escape_string($_POST['admin_name']);
    $email = $conn->real_escape_string($_POST['email']);

    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if passwords match
    if (!empty($password) && $password != $confirm_password) {
        $msg = "Passwords do not match.";
    } else {
        // Update admin information
        $query = "UPDATE admins SET admin_name='$admin_name', email='$email'";

        // If password is not empty, hash and update it
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $query .= ", password='$hashed_password'";
        }

        $query .= " WHERE admin_id='$admin_id'";

        if ($conn->query($query) === TRUE) {
            $msg = "Profile updated successfully.";
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}

// Fetch admin information to display in the form
$result = $conn->query("SELECT * FROM admins WHERE admin_id='$admin_id'");
$admin = $result->fetch_assoc();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h2 class="text-center heading-bg bg-dark text-white p-2">Update Profile</h2>
                <div class="p-4">
                    <?php if ($msg != ''): ?>
                        <div class="text-info">
                            <?php echo htmlspecialchars($msg); ?>
                        </div>
                    <?php endif; ?>
                    <form action="admin_profile.php" method="POST">
                        <div class="form-group">
                            <label for="admin_name">Full Name:</label>
                            <input type="text" class="form-control" id="admin_name" name="admin_name"
                                value="<?php echo htmlspecialchars($admin['admin_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password (leave blank to keep current password):</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>