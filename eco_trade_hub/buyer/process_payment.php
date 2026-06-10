<?php

include("../db_connection.php");
require_once('../vendor/autoload.php');

// Set your Stripe API keys (test keys from Stripe Dashboard)
\Stripe\Stripe::setApiKey('sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9'); // Replace with your test secret key

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $total_price = intval($_POST['total_price']);
    $token = $_POST['stripeToken'];

    try {
        // Create a charge: This will charge the user's card
        $charge = \Stripe\Charge::create([
            'amount' => $total_price * 100, // Amount in cents
            'currency' => 'usd',
            'description' => 'Order ' . $order_id,
            'source' => $token,
        ]);

        // Handle successful charge here (e.g., update order status in your database)

        // Redirect to a success page
        header('Location: payment_success.php?order_id=' . $order_id);
        exit();

    } catch (\Stripe\Exception\CardException $e) {
        // Handle card errors
        echo 'Error: ' . $e->getError()->message;
    }
}
?>
