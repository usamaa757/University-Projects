<?php
require('connectToMysql.php');
$email = $_POST['email'];
$password = $_POST['password'];
$hash = md5($password);
$sql = "SELECT * FROM users WHERE email='$email' AND password='$hash'";

$info = mysqli_query($connecti, $sql);
if (!$info) {
    printf("Error: %s\n", mysqli_error($connecti));
    exit();
}
$row_cnt = mysqli_num_rows($info);
if ($row_cnt == 0) {
    // header('Refresh:4; url=login.php');
    echo '<script>alert("Incorrect Email or Password!"); window.location="login.php"; </script>)';
}

if ($row_cnt > 0) {
    while ($row = mysqli_fetch_assoc($info)) {
        session_start();
        //$id = $row['id'];
        $_SESSION["userid"] = $row['user_id'];
        $_SESSION["name"] = $row["username"];
        $_SESSION["email"] = $row["email"];
        $_SESSION["uid"] = $password;
        $_SESSION["status"] = true;
        $_SESSION["isUser"] = 1;
        $_SESSION["isAdmin"] = 0;
        header("location: home.php");
        exit();
    }
    exit();
}
?>
