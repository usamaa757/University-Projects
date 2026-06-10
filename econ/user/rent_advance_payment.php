<?php

session_start();
require 'vendor/autoload.php';
require '../db.php';

\Stripe\Stripe::setApiKey('sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9');

$propertyId = $_POST['property_id'];
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("User not logged in.");
}

// Get property price
$stmt = $conn->prepare("SELECT price FROM properties WHERE id = ?");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Property not found.");
$property = $result->fetch_assoc();
$amount = $property['price'];
$amountCents = intval($amount * 100);
$adminFeeCents = intval($amount * 0.2 * 100); // 20%

// Get agent's Stripe account ID
$stmt = $conn->prepare("SELECT u.stripe_account_id FROM users u JOIN properties p ON p.agent_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$stmt->bind_result($agentStripeId);
$stmt->fetch();
$stmt->close();

if (!$agentStripeId) die("No Stripe ID found.");

// ✅ Create rental row (optional: only if your logic creates rental before payment)
$stmt = $conn->prepare("INSERT INTO rentals (property_id, user_id, monthly_rent, total_months, paid_months, status,
created_at) VALUES (?, ?, ?, 12, 0, 'active', NOW())");
$stmt->bind_param("iii", $propertyId, $userId, $amount);
$stmt->execute();
$rentalId = $stmt->insert_id;
$stmt->close();

// ✅ Create Stripe Checkout session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => ['name' => 'Advance Rent Payment'],
            'unit_amount' => $amountCents,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'payment_intent_data' => [
        'application_fee_amount' => $adminFeeCents,
        'transfer_data' => ['destination' => $agentStripeId],
    ],
    'success_url' => "http://localhost/econ/user/rent_success.php?property_id=$propertyId&rental_id=$rentalId",
    'cancel_url' => "http://localhost/econ/user/rent.php?id=$propertyId",
]);

header("Location: " . $session->url);
exit;
