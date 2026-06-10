<?php
// Initialize the session
session_start();
if($_SESSION['student_email']){
// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();


header("location: student/student_login.php");
}
else{
    header("location: admin/admin_login.php");
}
?>
