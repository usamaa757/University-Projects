<?php
include("config.php");
include("navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user id
if (!isset($_GET['id'])) {
    header("Location: management.php");
    exit();
}
$msg = $error = '';

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$user = mysqli_fetch_assoc($result);

// Update
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    mysqli_query($conn, "UPDATE users SET name='$name', email='$email' WHERE id=$id");
    $msg = "User updated successfully!";
}
?>
<div class="form-container">
    <h2>Edit user</h2>
    <?php if (!empty($msg)): ?>
        <div class="success-box">
            <p><?php echo $msg; ?></p>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <button type="submit" name="update">Update</button>
    </form>
</div>
</body>

</html>