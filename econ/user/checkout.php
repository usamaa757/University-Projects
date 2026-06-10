<?php
session_start();
require 'vendor/autoload.php';
require '../db.php';

\Stripe\Stripe::setApiKey('sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9');

// Get form data
$propertyId = $_POST['property_id'];
$balance = $_POST['remaining_balance'];
$tenure = $_POST['period_years'];
$rate = $_POST['markup_rate'];
$installment = $_POST['monthly_installment'];
$total = $_POST['total_payment'];

// STEP 1: Fetch the agent's Stripe connected account ID
$agentStripeId = '';
$stmt = $conn->prepare("SELECT u.stripe_account_id FROM users u JOIN properties p ON p.agent_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$stmt->bind_result($agentStripeId);
$stmt->fetch();
$stmt->close();

if (!$agentStripeId) {
    die("Agent Stripe account not found.");
}

// STEP 2: Convert PKR to smallest currency unit (Stripe expects cents, USD used)
$amountInCents = intval($installment) * 100;
$applicationFee = intval($installment * 0.08) * 100; // 8% fee to admin in cents

// STEP 3: Create Checkout session with fee and destination account
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd', // PKR not supported in live, use USD for test
            'product_data' => [
                'name' => 'Property Installment Payment',
            ],
            'unit_amount' => $amountInCents,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'payment_intent_data' => [
        'application_fee_amount' => $applicationFee, // 8% to admin
        'transfer_data' => [
            'destination' => $agentStripeId, // 92% to agent
        ],
    ],
    'success_url' => "http://localhost/econ/user/confirm_purchase.php?property_id={$propertyId}&balance={$balance}&tenure={$tenure}&rate={$rate}&installment={$installment}&total={$total}",
    'cancel_url' => "http://localhost/econ/user/buy.php?id={$propertyId}",
]);
header("Location: " . $session->url);
exit;