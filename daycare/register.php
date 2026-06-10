<?php
// parent_register.php
session_start();
require_once 'db_connection.php'; // include your DB connection file

$successMsg = $errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($name) && !empty($email) && !empty($password)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'parent')");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $successMsg = "Registration successful! You can now log in.";
        } else {
            $errorMsg = "Error: " . $stmt->error;
        }
    } else {
        $errorMsg = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Parent Registration</title>
    <style>
    body {
        font-family: Arial;
        background: #f0f0f0;
    }

    .container {
        width: 400px;
        margin: auto;
        padding: 20px;
        background: #fff;
        margin-top: 50px;
        border-radius: 8px;
        box-shadow: 0 0 10px #ccc;
    }

    h2 {
        text-align: center;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    input[type="submit"] {
        width: 100%;
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .msg {
        text-align: center;
        margin: 10px 0;
    }

    .error {
        color: red;
    }

    .success {
        color: green;
    }
    </style>
</head>

<body>

    <div class="container">
        <h2>Parent Registration</h2>

        <?php if ($successMsg) echo "<div class='msg success'>$successMsg</div>"; ?>
        <?php if ($errorMsg) echo "<div class='msg error'>$errorMsg</div>"; ?>

        <form method="POST" action="">
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Register">
        </form>
    </div>

</body>

</html>