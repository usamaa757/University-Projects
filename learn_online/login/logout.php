<?php
// Initialize the session
session_start();
if($_SESSION['student_email']){
// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page or any other page after logout
header("location: student_login.php");
}
else{
    header("location: admin_login.php");
}
?>
