<?php
require 'vendor/autoload.php';
require '../db.php';
session_start();

\Stripe\Stripe::setApiKey('sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9'); // your secret key

$purchaseId = $_POST['purchase_id'];
$installment = (int)$_POST['installment'];
$title = $_POST['title'];

// Fetch agent's stripe_account_id from purchase > property > user
$sql = "SELECT u.stripe_account_id 
        FROM purchases p 
        JOIN properties pr ON pr.id = p.property_id 
        JOIN users u ON u.id = pr.agent_id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $purchaseId);
$stmt->execute();
$stmt->bind_result($agentStripeId);
$stmt->fetch();
$stmt->close();

if (!$agentStripeId) {
    die("Agent Stripe account not found.");
}

$amountInCents = $installment * 100;
$applicationFee = $installment * 0.08 * 100;

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => 'Installment for: ' . $title,
            ],
            'unit_amount' => $amountInCents,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'payment_intent_data' => [
        'application_fee_amount' => (int)$applicationFee,
        'transfer_data' => [
            'destination' => $agentStripeId,
        ],
    ],
    'success_url' => "http://localhost/econ/user/installment_success.php?purchase_id={$purchaseId}&installment={$installment}",
    'cancel_url' => "http://localhost/econ/user/installments.php",
]);

header("Location: " . $session->url);
exit;