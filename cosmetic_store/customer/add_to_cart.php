<?php
session_start();
include '../db.php';

$customer_id = $_SESSION['customer_id'];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;

if (!$customer_id || !$product_id || $quantity < 1) {
    die("Invalid access.");
}

// Check if item is already in cart
$check_query = "SELECT * FROM cart WHERE customer_id = ? AND product_id = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $customer_id, $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // If product exists in cart, update quantity
    $update_query = "UPDATE cart SET quantity = quantity + ? WHERE cart_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ii", $quantity, $row['cart_id']);
    mysqli_stmt_execute($update_stmt);
} else {
    // If product does not exist in cart, insert new item with correct quantity
    $insert_query = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "iii", $customer_id, $product_id, $quantity);
    mysqli_stmt_execute($insert_stmt);
}

echo "<script>alert('Item added to cart!'); window.location.href='cart.php';</script>";