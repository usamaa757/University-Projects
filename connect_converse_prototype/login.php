<?php
require 'database_connection.php';
session_start();
$result = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hash_password);
        $stmt->fetch();
        if (password_verify($password, $hash_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: user.php");
            exit();
        } else {
            $result = "Invalid password.";
        }
    } else {
        $result = "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>


    <div class="container">
        <div>
            <a href="index.php" class="btn">Home</a>
            <a href="register.php" class="btn">Register</a>
            <a href="login.php" class="btn">Login</a>
        </div>
        <h3>Login</h3>
        <?php if (!empty($result)) { ?>
        <div><?= $result; ?></div>
        <?php } ?>
        <form class="form" method="post" action="login.php">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">
            <div class="button">
                <button type="submit" class="submit-button">Login</button>
                <button type="reset" class="clear-button">Clear</button>
            </div>
        </form>
    </div>
</body>

</html>