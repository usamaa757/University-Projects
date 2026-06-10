<?php
$GLOBALS['connecti'] = mysqli_connect("localhost", "root", "", "db_toysapp");

if (!$connecti) {
    die("Connection failed: " . mysqli_connect_error());
}
?>