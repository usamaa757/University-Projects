<?php
session_start();
$user_id = $_SESSION['user_id'];
require 'vendor/autoload.php';
include '../db.php';

\Stripe\Stripe::setApiKey('');

$token = $_POST['stripeToken'];
$order_id = $_POST['order_id'];
$amount = floatval($_POST['amount']);

$order = $conn->query("SELECT * FROM orders WHERE order_id = $order_id")->fetch_assoc();
if (!$order) {
    echo "Invalid order.";
    exit;
}

try {
    $customer = \Stripe\Customer::create([
        'email' => $order['email'],
        'source' => $token,
    ]);

    $charge = \Stripe\Charge::create([
        'amount' => $amount * 100,
        'currency' => 'usd',
        'customer' => $customer->id,
        'description' => 'Installment Payment for Order ' . $order_id,
    ]);

    $new_paid = $order['amount_paid'] + $amount;
    $status = ($new_paid >= $order['total_amount']) ? 'paid' : 'partial';

    $stmt = $conn->prepare("UPDATE orders SET amount_paid = ?, payment_status = ? WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("dsii", $new_paid, $status, $order_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Installment payment successful!');
            window.location.href = 'order_confirmation.php?order_id=$order_id';
        </script>";
        exit;
    } else {
        echo "DB Error: " . $stmt->error;
    }
} catch (Exception $e) {
    echo "<script>alert('Installment failed: " . $e->getMessage() . "'); window.location.href='order_confirmation.php?order_id=$order_id';</script>";
}
