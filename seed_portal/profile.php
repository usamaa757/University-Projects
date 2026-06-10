<?php
include("header.php");
include("db_connect.php");



$message = "";
$error = "";

$user_id = $_SESSION['user_id'];

// Fetch current user data
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Update user info
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    // If user entered new password → hash it
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET 
                        name='$name',
                        email='$email',
                        contact='$contact',
                        address='$address',
                        password='$hashed_password'
                        WHERE id='$user_id'";
    } else {
        // No password change
        $update_sql = "UPDATE users SET 
                        name='$name',
                        email='$email',
                        contact='$contact',
                        address='$address'
                        WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $update_sql)) {
        $message = "Profile updated successfully!";
        // Refresh user data after update
        $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2>Edit Profile</h2>
    <?php
    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } else {
        echo "<div class='message'>$message</div>";
    }
    ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" value="<?php echo $user['name']; ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo $user['email']; ?>" required>
        <input type="text" name="contact" placeholder="Contact Number" value="<?php echo $user['contact']; ?>">
        <textarea name="address" placeholder="Address" rows="3"><?php echo $user['address']; ?></textarea>
        <input type="password" name="password" placeholder="New Password (leave blank to keep old)">
        <div class="text-center">
            <button type="submit" class="btn">Update Profile</button>
        </div>
    </form>

    <p style="text-align:center;margin-top:10px;">
        <a href="user_dashboard.php">Back to Dashboard</a>
    </p>
</div>

</body>

</html>