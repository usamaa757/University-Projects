<?php
session_start();
require 'vendor/autoload.php';
include '../db.php';

\Stripe\Stripe::setApiKey('');

// Sanitize and prevent SQL injection by using prepared statements
$order_id = (int)$_GET['order_id'];

// Fetch order details from the database
$order = $conn->query("SELECT * FROM orders WHERE order_id = $order_id")->fetch_assoc();

// Check if the order exists
if (!$order) {
    echo "<script>alert('Order not found.'); window.location.href='order_list.php';</script>";
    exit();
}

$order_time = new DateTime($order['order_time']);
$now = new DateTime();
$hours = $now->diff($order_time)->h + ($now->diff($order_time)->days * 24);

// Only allow cancellation within 24 hours
if ($hours <= 24 && $order['status'] !== 'cancelled') {
    if ($order['payment_status'] !== 'cancelled' && !empty($order['stripe_charge_id'])) {
        try {
            // Attempt to refund the payment
            \Stripe\Refund::create([
                'charge' => $order['stripe_charge_id'],
            ]);
            https: //meet.google.com/uqh-jrbk-eqj
            // Update the order status to 'cancelled' and payment status to 'refunded'
            $updateQuery = $conn->prepare("UPDATE orders SET status='cancelled', payment_status='refunded' WHERE order_id=?");
            $updateQuery->bind_param("i", $order_id);
            $updateQuery->execute();

            // Provide success feedback
            echo "<script>alert('Order cancelled and refunded successfully.'); window.location.href='orders.php';</script>";
            exit();
        } catch (Exception $e) {
            // Handle any errors during refund
            echo "<script>alert('Refund failed: " . $e->getMessage() . "'); window.location.href='orders.php';</script>";
            exit();
        }
    } else {
        // No valid Stripe charge to refund
        echo "<script>alert('No valid Stripe charge to refund.'); window.location.href='orders.php';</script>";
        exit();
    }
} else {
    // Handle case where the cancellation period has expired
    echo "<script>alert('Cancellation period expired.'); window.location.href='orders.php';</script>";
    exit();
}