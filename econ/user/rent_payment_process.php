<?php
session_start();
require 'vendor/autoload.php';
require '../db.php';

// Stripe Secret Key
\Stripe\Stripe::setApiKey('sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9');

// Get form data
$rentalId = $_POST['rental_id'];
$amount = $_POST['amount']; // Rent amount
$propertyId = $_POST['property_id']; // Needed to find agent

// STEP 1: Get agent's Stripe ID for this property
$stmt = $conn->prepare("
    SELECT u.stripe_account_id 
    FROM users u 
    JOIN properties p ON p.agent_id = u.id 
    WHERE p.id = ?
");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$stmt->bind_result($agentStripeId);
$stmt->fetch();
$stmt->close();

if (!$agentStripeId) {
    die("Agent Stripe account not found.");
}

// STEP 2: Convert PKR to USD cents (Stripe doesn't support PKR)
$amountInCents = intval($amount) * 100;
$adminFee = intval($amount * 0.05) * 100; // 5% to admin

// STEP 3: Create Checkout Session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => 'Monthly Rent Payment',
            ],
            'unit_amount' => $amountInCents,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'payment_intent_data' => [
        'application_fee_amount' => $adminFee,
        'transfer_data' => [
            'destination' => $agentStripeId,
        ],
    ],
    'success_url' => "http://localhost/econ/user/rent_success.php?rental_id={$rentalId}",
    'cancel_url' => "http://localhost/econ/user/rent_payment.php",
]);

header("Location: " . $session->url);
exit;
