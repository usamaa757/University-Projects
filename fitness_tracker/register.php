<?php 

include("navbar.php"); 
include("db.php");

$error = "";
$msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password != $confirm_password){
            $error = "Password not matched";

    }
    else{
    // Encrypt password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0){
        $error = "Email already registered";
    } 
    else {
        // Insert user
        $sql = "INSERT INTO users (full_name, email, address, role, password) 
                VALUES ('$full_name', '$email', '$address', '$role', '$hashed_password')";

        if(mysqli_query($conn, $sql)){
            $msg = "Registration Successful!";
        } else {
            $error = "Error: Could not register";
        }
    }
}
}

?>


<div class="form-container">
    <h2>Create Account</h2>

    <?php if($error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <?php if($msg != "") { ?>
        <p class="message"><?php echo $msg; ?></p>
    <?php } ?>

    <form action="" method="POST">

        <input type="text" name="full_name" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="text" name="address" placeholder="Address" required>

        <select name="role" required style="width:100%; padding:14px; margin:10px 0; border-radius:8px; border:1px solid #cbd5e1; font-size:15px;">
            <option value="">Select Role</option>
            <option value="user">User</option>
            <option value="trainer">Trainer</option>
        </select>

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
<div class="text-center">

    <button type="submit">Register</button>
</div>

    </form>
</div>

</body>
</html>
