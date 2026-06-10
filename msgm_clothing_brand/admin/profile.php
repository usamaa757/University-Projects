<?php
include 'header.php';
include '../db_connection.php';

// Fetch user data from the database to pre-fill the form
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Incorrect current password.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
    } else {
        // Update name and email
        $update_query = "UPDATE admins SET admin_name = ?, email = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $name, $email, $admin_id);
        $stmt->execute();

        // Update password if a new password is provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
            $stmt->bind_param("si", $hashed_password, $admin_id);
            $stmt->execute();
        }


        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    }
}
?>


<div class="container mt-4 round shadow border" style="max-width: 600px;">
    <div class="text-center p-3">

        <h3>Edit Profile</h3>
    </div>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php elseif (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <!-- Name -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name"
                value="<?php echo htmlspecialchars($user['admin_name']); ?>" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <!-- Phone -->
        <div class="form-group">
            <label for="phone">Email</label>
            <input type="tel" class="form-control" id="phone" name="phone"
                value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        <!-- Current Password -->
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>

        <!-- New Password -->
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
        </div>

        <!-- Confirm New Password -->
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>

        <div class="text-center mb-3">

            <button type="submit" class="btn bg-primary text-white">Save Changes</button>
        </div>
</div>
</form>

</div>
</body>

</html>