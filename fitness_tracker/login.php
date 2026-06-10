<?php
include("db.php");
include("navbar.php");

$error = "";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate empty fields
    if ($email == "" || $password == "") {
        $error = "All fields are required.";
    } else {
        // Fetch user
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

             
                    header("Location: dashboard.php");
                
                exit();

            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }
    }
}
?>



<div class="form-container">
    <h2>Login</h2>

    <?php if($error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <?php if($msg != "") { ?>
        <p class="message"><?php echo $msg; ?></p>
    <?php } ?>

    <form action="" method="POST">

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="password" name="password" placeholder="Password" required>
<div class="text-center">

    <button type="submit">Login</button>
</div>

    </form>
</div>
