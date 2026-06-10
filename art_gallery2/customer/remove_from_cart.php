<?php
session_start();

$art_id = $_GET['art_id'];

if (isset($_SESSION['cart'][$art_id])) {
    if ($_SESSION['cart'][$art_id] > 1) {
        $_SESSION['cart'][$art_id]--;
    } else {
        unset($_SESSION['cart'][$art_id]);
    }
}

header("Location: cart.php");
exit();