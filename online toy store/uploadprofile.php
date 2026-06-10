<?php
require('connectToMysql.php');

$username = filter_input(INPUT_POST, 'username');
$password = $_POST['password'];
$email = filter_input(INPUT_POST, 'email');
$contact = filter_input(INPUT_POST, 'contact');
$hash = md5($password);
$userid = $_POST["user_id"];

$res = mysqli_query($connecti, 'SELECT * FROM users where user_id = "' . $userid . '"');
if ($res && mysqli_num_rows($res) > 0) {
    $sql = "UPDATE users SET username='$username', password='$hash', email='$email', contact='$contact' WHERE user_id='$userid' ";
    $info = mysqli_query($connecti, $sql);

    if ($info) {
        session_start();
        $_SESSION["name"] = $username;
        $_SESSION["email"] = $email;
        $_SESSION["uid"] = $password;
        $_SESSION["status"] = true;
        $_SESSION["isAdmin"] = 0;

        echo "<script type='text/javascript'>alert('Profile Updated Successfully!')</script>";
        header("refresh:3;url=updateprofile.php");
    } else {
        echo "<script type='text/javascript'>alert('Failed to Update Profile')</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Error')</script>";
}
?>