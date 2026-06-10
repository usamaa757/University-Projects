<?php
include("config.php");
include("navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$msg = $error = '';
$user_id = $_SESSION['user_id'];

// Fetch current user data
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $error = "User not found!";
}

// Handle profile update
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $password = !empty($_POST['password']) ? md5($_POST['password']) : $user['password'];

    // Only update account info if user is a seller
    if ($user['role'] == 'seller') {
        $account_no = trim($_POST['account_no']);
        $account_detail = trim($_POST['account_detail']);
        $sql = "UPDATE users SET 
                    name='$name', 
                    email='$newEmail', 
                    password='$password', 
                    account_no='$account_no', 
                    account_detail='$account_detail'
                WHERE id='$user_id'";
    } else {
        $sql = "UPDATE users SET 
                    name='$name', 
                    email='$newEmail', 
                    password='$password'
                WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['user'] = $newEmail; // update session if email changed
        $msg = "Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Something went wrong. Try again!";
    }
}
?>

<div class="form-container">
    <h2>My Profile</h2>

    <?php if (!empty($msg)): ?>
    <div class="success-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <?php if ($user['role'] == 'seller'): ?>
        <input type="number" name="account_no" value="<?php echo htmlspecialchars($user['account_no']); ?>" required>
        <input type="text" name="account_detail" value="<?php echo htmlspecialchars($user['account_detail']); ?>"
            required>
        <?php endif; ?>

        <input type="password" name="password" placeholder="New Password (leave blank to keep old)">
        <button type="submit" name="update">Update Profile</button>
    </form>
</div>