<?php
session_start();
include '../db.php';

$cart_id = $_GET['cart_id'] ?? null;

if ($cart_id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE cart_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $cart_id);
    mysqli_stmt_execute($stmt);
}

header("Location: cart.php");
exit;