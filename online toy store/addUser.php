<?php

$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');
$email = filter_input(INPUT_POST, 'email');
$contact = filter_input(INPUT_POST, 'contact');
$hash = md5($password);
if ($username == "" || $password == "" || $email == "" || $contact == "") {
    echo 'The Fields cannot be empty:';
}
if ($username == "") {
    echo '<br>Username';
    exit();
}
if ($password == "") {
    echo '<br>Password';
    exit();
}
if ($email == "") {
    echo '<br>Email';
    exit();
}
if ($contact == "") {
    echo '<br>Contact';
    exit();
} else {
    $connecti = mysqli_connect("localhost", "root", "", "db_toysapp");
    $res = mysqli_query($connecti, 'select email from users where email = "' . $email . '"');
    if ($res && mysqli_num_rows($res) > 0) {
        print '<h3>' . $email . '</h3>';
        echo '<h3><br>already exists</h3>';
        echo '<h3><br>Enter a different Email!</h3>';
        header("refresh:4;url=signup.php");
        exit();
    } else {
        $sql = "INSERT INTO users(username, password, email, contact) VALUES ('$username', '$hash', '$email', '$contact' )";

        if (mysqli_query($connecti, $sql)) {
            echo "<script type='text/javascript'>alert('A account has been created for you succesfully! Please Login')</script>";
            header("refresh:3;url=login.php");
        } else {
            echo "<script type='text/javascript'>alert('Something went wrong please try again!')</script>";
        }
    }
}
?>