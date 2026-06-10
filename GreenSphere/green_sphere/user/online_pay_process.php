<?php
require('config.php');
require('vendor/autoload.php'); // Include the Stripe PHP library
include 'header.php';

\Stripe\Stripe::setApiKey($secretKey); // Ensure $secretKey is set in config.php

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
		!isset($order['plant']['plant_id'], $order['user']['email'], $order['total_price'], $order['quantity'])
	) {
		die("Incomplete order details. Please try again.");
	}

	$plant_id = $order['plant']['plant_id'];
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
		// Start a transaction
		$conn->begin_transaction();

		// Insert order details into the database
		$insert_order_query = "
			INSERT INTO orders (user_id, plant_id, quantity, total_price, payment_method, ship_address, payment_status, order_date)
			VALUES (?, ?, ?, ?, ?, 'Online', 'Paid', NOW())
		";
		$order_stmt = $conn->prepare($insert_order_query);
		$order_stmt->bind_param('iiiss', $order['user']['user_id'], $plant_id, $quantity, $total_price, $ship_address);
		$order_stmt->execute();

		// Update stock quantity for the plant
		$plant_query = "UPDATE plants SET quantity = quantity - ? WHERE plant_id = ?";
		$stmt = $conn->prepare($plant_query);
		$stmt->bind_param('ii', $quantity, $plant_id);
		$stmt->execute();

		// Remove the item from the cart
		$remove_cart_item_query = "DELETE FROM cart WHERE user_id = ? AND plant_id = ?";
		$remove_cart_stmt = $conn->prepare($remove_cart_item_query);
		$remove_cart_stmt->bind_param('ii', $order['user']['user_id'], $plant_id);
		$remove_cart_stmt->execute();

		// Commit the transaction
		$conn->commit();

		// Proceed with Stripe payment after successful DB operations
		$charge = \Stripe\Charge::create([
			'amount' => $total_price * 100, // Amount in cents
			'currency' => 'pkr',
			'description' => 'Payment for Plant Purchase',
			'source' => $token,
			'receipt_email' => $user_email,
		]);

		// Clear the order session data
		unset($_SESSION['order']);

		// Display confirmation message
		echo "<div class='container mt-5 text-center'>";
		echo "<h3>Thank you for your purchase!</h3>";
		echo "<p>Your payment of Rs {$total_price} has been successfully processed.</p>";
		echo "<a href='order_details.php' class='btn btn-primary'>View your order details</a>";
		echo "</div>";
	} catch (Exception $e) {
		// Rollback the transaction if any error occurs
		$conn->rollback();

		// Handle errors
		echo "<div class='container mt-5 text-center'>";
		echo "<h3>Error</h3>";
		echo "<p>There was an error: " . $e->getMessage() . "</p>";
		echo "<a href='cart.php' class='btn btn-primary'>Return to Cart</a>";
		echo "</div>";

		// Optionally, log the error for further investigation
		error_log($e->getMessage());
	}
} else {
	echo "No payment token received.";
}