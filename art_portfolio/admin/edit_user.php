<?php
include 'header.php';
include '../db.php';

$id = $_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id'"));

$message = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $role = $_POST['role'];
    $password = trim($_POST['password']);

    $update_query = "UPDATE users SET name = '$name', role = '$role'";

    // If password is entered, hash and update
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update_query .= ", password_hash = '$hashed'";
    }

    $update_query .= " WHERE user_id = '$id'";

    $update = mysqli_query($conn, $update_query);
    $message = $update ? "User updated." : "Update failed.";

    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id'"));
}
?>


<h2>Edit User</h2>

<div class="form-container">
    <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" class="forms">
        Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>
        Role:
        <select name="role">
            <option value="artist" <?= $user['role'] == 'artist' ? 'selected' : '' ?>>Artist</option>
            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
        </select><br><br>
        Password (leave blank to keep unchanged):<br>
        <input type="password" name="password"><br><br>
        <div class="text-center">
            <button type="submit" class="btn">Update</button>
        </div>
    </form>
</div>