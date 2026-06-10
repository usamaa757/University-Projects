<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
include '../db.php';

// Stripe secret key
\Stripe\Stripe::setApiKey('');

// Ensure cart exists
if (!isset($_SESSION['cart'])) {
    echo "<script>alert('Cart is empty!'); window.location.href='art_list.php';</script>";
    exit;
}

// Validate POST
if (!isset($_POST['stripeToken'])) {
    echo "<script>alert('Stripe token missing.'); window.location.href='checkout.php';</script>";
    exit;
}

$cart = $_SESSION['cart'];
$art_id = $cart['art_id'];
$token = $_POST['stripeToken'];

// Validate cart fields
if (!isset($cart['email'], $cart['phone'], $cart['address'])) {
    echo "<script>alert('Cart information incomplete.'); window.location.href='checkout.php';</script>";
    exit;
}

// Fetch art details
$art = $conn->query("SELECT * FROM arts WHERE art_id = $art_id")->fetch_assoc();
$total_price = $art['price'];

try {
    // Create Stripe customer
    $customer = \Stripe\Customer::create([
        'email' => $cart['email'],
        'source' => $token,
    ]);

    // Charge customer
    $charge = \Stripe\Charge::create([
        'amount' => $total_price * 100,
        'currency' => 'usd',
        'description' => 'Art Purchase - ' . $art['art_name'],
        'customer' => $customer->id,
    ]);

    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (
            art_id, customer_id, customer_email, phone, address,
            total_price, payment_method, shipping_status, order_date, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");

    $status = 'Paid';
    $payment_method = 'Online';
    $shipping_status = 'Pending';
    $customer_id = $_SESSION['user_id'];

    $stmt->bind_param(
        "iisssdsss",
        $art_id,
        $customer_id,
        $cart['email'],
        $cart['phone'],
        $cart['address'],
        $total_price,
        $payment_method,
        $shipping_status,
        $status
    );

    if ($stmt->execute()) {
        unset($_SESSION['cart']);
        $order_id = $conn->insert_id;
        echo "<script>
            alert('Order placed successfully!');
            window.location.href = 'order_confirmation.php?order_id=$order_id';
        </script>";
        exit();
    } else {
        throw new Exception("Failed to save order.");
    }
} catch (Exception $e) {
    echo "<script>alert('Payment failed: " . $e->getMessage() . "'); window.location.href='checkout.php';</script>";
}
