<?php
session_start();

$art_id = $_GET['art_id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if (isset($_SESSION['cart'][$art_id])) {
    $_SESSION['cart'][$art_id]++;
} else {
    $_SESSION['cart'][$art_id] = 1;
}

header("Location: cart.php");
exit();