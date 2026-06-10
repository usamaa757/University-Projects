<?php

include '../db.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);


    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting product'); window.location.href='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Invalid product ID'); window.location.href='dashboard.php';</script>";
}
