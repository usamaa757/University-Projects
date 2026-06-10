<?php
session_start();
require '../db.php';
require_once 'vendor/autoload.php'; // Stripe SDK

\Stripe\Stripe::setApiKey("sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $cart_items = $_SESSION['cart_items'];
        $total_price = $_SESSION['total_price'];
        $customer_id = $_SESSION['user_id'];
        $email = $_SESSION['email'];
        $address = $_SESSION['address'];
        $phone = $_SESSION['phone'];
        $name = $_SESSION['name'];
        $payment_method = 'Online';
        $status = 'Paid';
        if (empty($cart_items) || !$email || !$total_price) {
            throw new Exception("Invalid order session data.");
        }

        // Create Stripe charge
        $charge = \Stripe\Charge::create([
            "amount" => $total_price * 100,
            "currency" => "usd",
            "description" => "Purchase of Artworks",
            "source" => $_POST['stripeToken'],
            "receipt_email" => $email,
            "metadata" => [
                "customer_id" => $customer_id,
                "email" => $email,
                "name" => $name
            ]
        ]);

        if ($charge->status === "succeeded") {
            foreach ($cart_items as $item) {
                $stmt = $conn->prepare("INSERT INTO orders (art_id, customer_id, customer_email, phone, address, total_price, payment_method, status) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param("iisssdss", $item['art_id'], $customer_id, $email, $phone, $address, $total_price, $payment_method, $status);
                $stmt->execute();
                $stmt->close();
            }

            // Clear cart
            unset($_SESSION['cart']);
            unset($_SESSION['cart_items']);

            echo "<script>alert('Payment successful! Order placed.'); window.location='order_history.php';</script>";
            exit;
        } else {
            throw new Exception("Payment failed.");
        }
    } catch (Exception $e) {
        error_log("Payment error: " . $e->getMessage());
        $_SESSION['payment_error'] = $e->getMessage();
        header("Location: payment_failed.php");
        exit;
    }
} else {
    die("Invalid access.");
}
