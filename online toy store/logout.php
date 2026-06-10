<?php
session_start();
$_SESSION = array();    //session_destroy();	
// remove all session variables
session_unset();
// destroy the session
session_destroy();
require('header.php');
?>
<div class="container" align="center">
<br><br><h3>You have successfully Logged Out</h3>
    <br><br><br>
    <div>
        <h4><a href="index.php">Continue Browsing</h4></a>
    </div>
</div>
<?php require('footer.php'); ?>