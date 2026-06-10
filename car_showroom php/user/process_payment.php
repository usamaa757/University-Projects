<?php
session_start();
require 'vendor/autoload.php';
include '../db.php';
$user_id = $_SESSION['user_id'];
\Stripe\Stripe::setApiKey('');

$token = $_POST['stripeToken'];

$cart = $_SESSION['cart'];
$car_id = $cart['car_id'];

$car = $conn->query("SELECT * FROM cars WHERE car_id = $car_id")->fetch_assoc();
$city = $conn->query("SELECT * FROM cities WHERE city_id = " . $cart['city_id'])->fetch_assoc();

$total_amount = $car['price'] + $city['delivery_charge'];
$installment_plan = $cart['installment_plan'];
$payment_option = $cart['payment_option'];
$paid_amount = 0;
$stripe_charge_id = null;

try {
    if ($payment_option === 'installment' && $installment_plan > 1) {
        $paid_amount = round($total_amount / $installment_plan, 2); // Pay first installment
    } else {
        $paid_amount = $total_amount; // Full payment
    }
    $customer = \Stripe\Customer::create([
        'email' => $cart['email'],
        'source' => $token,
    ]);
    $stripe_customer_id = $customer->id;

    // Create Stripe Charge
    $charge = \Stripe\Charge::create([
        'amount' => $paid_amount * 100,
        'currency' => 'usd',
        'description' => 'Car Purchase - ' . $car['model'],
        'customer' => $stripe_customer_id,
    ]);

    $stripe_charge_id = $charge->id;

    // Store Order
    $query = $conn->prepare("
        INSERT INTO orders 
        (car_id, name, email, phone, city_id, payment_option, installment_plan, total_amount, amount_paid, stripe_charge_id, stripe_customer_id, payment_status, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $payment_status = ($payment_option === 'full') ? 'paid' : 'partial';

    $query->bind_param(
        "isssssiddsssi",
        $car_id,
        $cart['name'],
        $cart['email'],
        $cart['phone'],
        $cart['city_id'],
        $payment_option,
        $installment_plan,
        $total_amount,
        $paid_amount,
        $stripe_charge_id,
        $stripe_customer_id,
        $payment_status,
        $user_id
    );

    if ($query->execute()) {
        unset($_SESSION['cart']);
        echo "<script>
        alert('Place order successful!');
        window.location.href = 'order_confirmation.php?order_id=$conn->insert_id';
    </script>";
        exit();
    } else {
        throw new Exception("Database error");
    }
} catch (Exception $e) {
    echo "<script>alert('Payment failed: " . $e->getMessage() . "'); window.location.href='checkout.php';</script>";
}