<?php
session_start();

// Determine the type of user before unsetting the session
$isAdmin = isset($_SESSION["admin_id"]);

session_unset();
session_destroy();

// Redirect based on the user type
if ($isAdmin) {
    header("Location: admin/admin_login.php");
} else {
    header("Location: voter/voter_login.php");
}
exit();
?>
