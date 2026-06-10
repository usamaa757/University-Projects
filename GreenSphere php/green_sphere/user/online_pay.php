<?php
include 'header.php';

// Include Stripe configuration file
require_once 'config.php'; // This file contains the Stripe API key


// Check if order details are stored in the session
if (!isset($_SESSION['order'])) {
    echo "<p>No order data found. Please try again.</p>";
    exit;
}

$order = $_SESSION['order'];
$total_price = $order['total_price'];
$email = $order['user']['email'];
?>


<div class="container mt-5 round border shadow p-4 text-center" style="max-width: 500px;">
    <h3>Complete Your Payment</h3>
    <p>Total Amount: Rs <?php echo htmlspecialchars($total_price); ?></p>

    <!-- Stripe Payment Form -->
    <form action="online_pay_process.php" method="POST">
        <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="<?php echo $publishableKey; ?>" data-amount="<?php echo $total_price * 100; ?>"
            data-currency="PKR" data-name="Plant Purchase" data-description="Payment for your selected plants"
            data-email="<?php echo htmlspecialchars($email); ?>" data-locale="auto" data-label="Pay Now (PKR)">
        </script>
    </form>
</div>

</body>

</html>