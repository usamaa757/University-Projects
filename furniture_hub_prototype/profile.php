<?php
include("config.php");
include("navbar.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$msg = $error = '';


$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $password = !empty($_POST['password']) ? md5($_POST['password']) : $user['password'];

    $sql = "UPDATE users SET name='$name', email='$newEmail', password='$password' WHERE id= '$user_id' ";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['user'] = $newEmail; // update session if email changed
        $msg = "Profile updated successfully!";
    }
}
?>

<div class="form-container">
    <h2>My Profile</h2>
    <?php if (!empty($msg)): ?>
        <div class="success-box">
            <p><?php echo $msg; ?></p>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <input type="password" name="password" placeholder="New Password (leave blank to keep old)">
        <button type="submit" name="update">Update Profile</button>
    </form>
</div>

</body>

</html>