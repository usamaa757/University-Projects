<?php
$base_url = "http://localhost/online_book_exchange/";
session_start();
session_unset();
session_destroy();
header("Location: " . $base_url . "login.php");
exit();