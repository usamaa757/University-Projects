<?php
require('../connectToMysql.php');
$email = $_POST['email'];
$password = $_POST['password'];
$hash = md5($password);
$sql = "SELECT * FROM admin WHERE email='$email' AND password='$hash'";

$info = mysqli_query($connecti, $sql);
if (!$info) {
    printf("Error: %s\n", mysqli_error($connecti));
    exit();
}
$row_cnt = mysqli_num_rows($info);
if ($row_cnt == 0) {
    // header('Refresh:4; url=adminlogin.php');
    echo '<script>alert("Incorrect Email or Password!"); window.location="adminlogin.php"; </script>)';
}

if (mysqli_num_rows($info) > 0) {
    while ($row = mysqli_fetch_assoc($info)) {
        session_start();
        $_SESSION["userid"] = $row['id'];
        $_SESSION["name"] = $row["name"];
        $_SESSION["email"] = $email;
        $_SESSION["uid"] = $password;
        $_SESSION["status"] = true;
        $_SESSION["isAdmin"] = 1;
        $_SESSION["isUser"] = 0;
        header("location: admin.php");
        exit();
    }
    exit();
}
?>