<?php
include 'header.php';
include '../db.php';

$message = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (name, email, password_hash, role) VALUES ('$name', '$email', '$hash', '$role')");
        $message = $insert ? "User added." : "Insert failed.";
    }
}
?>

<h2>Add New User</h2>
<div class="form-container">

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" class="forms">
        Name: <input type="text" name="name" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        Role:
        <select name="role">
            <option value="artist">Artist</option>
            <option value="user">User</option>
        </select><br><br>


        <div class="text-center">
            <button type="submit" class="btn">Add</button>
        </div>
    </form>
</div>