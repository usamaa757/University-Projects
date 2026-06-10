<?php require('../header.php'); 
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
}?>

<div align="center">
    <br><br><h2>Welcome To Admin Panel</h2>
</div>
<?php require('../footer.php'); ?>