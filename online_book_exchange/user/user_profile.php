<?php

include("header.php");
include("../db_connection.php");



$user_id = $_SESSION['user_id'];
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $conn->real_escape_string($_POST['user_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if passwords match
    if (!empty($password) && $password != $confirm_password) {
        $msg = "Passwords do not match.";
    } else {
        // Update user information
        $query = "UPDATE users SET user_name='$user_name', email='$email', contact_number='$contact_number', address='$address'";

        // If password is not empty, hash and update it
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $query .= ", password='$hashed_password'";
        }

        $query .= " WHERE user_id='$user_id'";

        if ($conn->query($query) === TRUE) {
            $msg = "Profile updated successfully.";
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}

// Fetch user information to display in the form
$result = $conn->query("SELECT * FROM users WHERE user_id='$user_id'");
$user = $result->fetch_assoc();
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
                    <form action="user_profile.php" method="POST">
                        <div class="form-group">
                            <label for="user_name">Full Name:</label>
                            <input type="text" class="form-control" id="user_name" name="user_name"
                                value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number:</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number"
                                value="<?php echo htmlspecialchars($user['contact_number']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="<?php echo htmlspecialchars($user['address']); ?>" required>
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