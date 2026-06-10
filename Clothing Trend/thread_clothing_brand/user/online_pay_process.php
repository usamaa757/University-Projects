<?php
require('config.php');
require('vendor/autoload.php');
include 'header.php';

\Stripe\Stripe::setApiKey($secretKey);

if (isset($_POST['stripeToken'])) {
	\Stripe\Stripe::setVerifySslCerts(false);
	$token = $_POST['stripeToken'];

	// Check if order session exists
	if (!isset($_SESSION['order'])) {
		die("Order details not found. Please retry the process.");
	}

	$order = $_SESSION['order'];

	// Validate session keys
	if (
		!isset($order['cloth']['cloth_id'], $order['user']['email'], $order['total_price'], $order['quantity'])
	) {
		die("Incomplete order details. Please try again.");
	}

	$cloth_id = $order['cloth']['cloth_id'];
	$user_email = $order['user']['email'];
	$total_price = $order['total_price'];
	$quantity = $order['quantity'];
	$ship_address = $order['ship_address'] ?? 'N/A';

	// Validate order_id in the URL
	// if (!isset($_GET['order_id'])) {
	// 	die("Order ID missing. Please retry the process.");
	// }
	// $order_id = $_GET['order_id'];

	try {
		// Create the charge via Stripe API
		$charge = \Stripe\Charge::create([
			'amount' => $total_price * 100, // Amount in cents
			'currency' => 'pkr',
			'description' => 'Payment for cloth Purchase',
			'source' => $token,
			'receipt_email' => $user_email,
		]);

		// Database actions
		include '../db_connection.php';

		// Insert order details into the database
		$insert_order_query = "
            INSERT INTO orders (user_id, cloth_id, quantity, total_price, ship_address, payment_status, order_date)
            VALUES (?, ?, ?, ?, ?, 'Paid', NOW())
        ";
		$order_stmt = $conn->prepare($insert_order_query);
		$order_stmt->bind_param('iiiss', $order['user']['user_id'], $cloth_id, $quantity, $total_price, $ship_address);
		$order_stmt->execute();

		// Update stock quantity for the cloth
		$cloth_query = "UPDATE cloths SET quantity = quantity - ? WHERE cloth_id = ?";
		$stmt = $conn->prepare($cloth_query);
		$stmt->bind_param('ii', $quantity, $cloth_id);
		$stmt->execute();

		// Remove the item from the cart
		$remove_cart_item_query = "DELETE FROM cart WHERE user_id = ? AND cloth_id = ?";
		$remove_cart_stmt = $conn->prepare($remove_cart_item_query);
		$remove_cart_stmt->bind_param('ii', $order['user']['user_id'], $cloth_id);
		$remove_cart_stmt->execute();

		// Clear the order session data
		unset($_SESSION['order']);

		// Display confirmation message
		echo "<div class='container mt-5 text-center'>";
		echo "<h3>Thank you for your purchase!</h3>";
		echo "<p>Your payment of Rs {$total_price} has been successfully processed.</p>";
		echo "<a href='order_details.php' class='btn btn-primary'>View your order details</a>";
		echo "</div>";
	} catch (\Stripe\Exception\CardException $e) {
		// Handle card-related errors
		echo "<div class='container mt-5 text-center'>";
		echo "<h3>Error</h3>";
		echo "<p>Payment failed: " . $e->getError()->message . "</p>";
		echo "<a href='cart.php' class='btn btn-primary'>Return to Cart</a>";
		echo "</div>";
	} catch (Exception $e) {
		// Handle other errors
		echo "<div class='container mt-5 text-center'>";
		echo "<h3>Error</h3>";
		echo "<p>There was an error processing your payment: " . $e->getMessage() . "</p>";
		echo "<a href='cart.php' class='btn btn-primary'>Return to Cart</a>";
		echo "</div>";
	}
} else {
	echo "No payment token received.";
}
