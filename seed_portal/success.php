<?php

include 'header.php';
include 'db_connect.php';

if (!isset($_GET['seed_id']) || !isset($_GET['quantity'])) {
    die("Invalid request.");
}

$seed_id = intval($_GET['seed_id']);
$quantity = intval($_GET['quantity']);
$total_price = intval($_GET['total_price']);
$seed_name = htmlspecialchars($_GET['seed_name']);

// Reduce stock
$conn->query("UPDATE seeds SET quantity_available = quantity_available - $quantity WHERE seed_id = $seed_id");

// Log order (optional)
$user_email = isset($_SESSION['user_email']);
$user_id = isset($_SESSION['user_id']);
$user_address = isset($_SESSION['user_address']);
$user_name = isset($_SESSION['user_name']);

$conn->query("INSERT INTO orders (seed_id, user_id, user_name, user_email, user_address, seed_name, total_price, quantity, order_date, status) 
              VALUES ($seed_id, '$user_id', '$user_name', '$user_email', '$user_address', '$seed_name', '$total_price', '$quantity', NOW(), 'Paid')");
?>
<div class="container">

    <h2>Payment Successful!</h2>
    <p>Thank you for your purchase.</p>
    <a href="seeds.php" class="btn">Back to Seeds</a>
    <a href="my_orders.php" class="btn">Goto Order List</a>
</div>
</body>

</html>