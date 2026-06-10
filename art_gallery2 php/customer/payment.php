<?php

include 'header.php';
include '../db.php';

// Validate POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid access.");
}

$payment_method = $_POST['payment_method'];
$_SESSION['name'] = $_POST['name'];
$_SESSION['email'] = $_POST['email'];
$_SESSION['address'] = $_POST['address'];
$_SESSION['phone'] = $_POST['phone'];

$name = $_SESSION['name'];
$email = $_SESSION['email'];
$address = $_SESSION['address'];
$phone = $_SESSION['phone'];

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    die("Cart is empty. Please add items before checkout.");
}

$total_price = 0;
$cart_items = [];

// Fetch all cart items from DB
$ids = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT * FROM art_items WHERE art_id IN ($ids)";
$result = $conn->query($sql);

while ($artwork = $result->fetch_assoc()) {
    $art_id = $artwork['art_id'];
    $quantity = $_SESSION['cart'][$art_id];
    $subtotal = $artwork['price'] * $quantity;
    $total_price += $subtotal;

    // Store items in session for Stripe processing
    $cart_items[] = [
        'art_id' => $art_id,
        'art_name' => $artwork['art_name'],
        'price' => $artwork['price'],

    ];
}

$_SESSION['cart_items'] = $cart_items;
$_SESSION['total_price'] = $total_price;

// Handle Cash on Delivery
if ($payment_method == 'Cash on Delivery') {
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("INSERT INTO orders (art_id, customer_id, customer_email, phone, address, total_price, payment_method) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("iisssds", $item['art_id'], $_SESSION['user_id'], $email, $phone, $address, $total_price, $payment_method);
        $stmt->execute();
        $stmt->close();
    }

    unset($_SESSION['cart']); // Clear cart after order placement

    echo "<script>alert('Order placed successfully! Payment on delivery.'); window.location='order_history.php';</script>";
    exit();
}

// Handle Online Payment via Stripe
?>

<script src="https://js.stripe.com/v3/"></script>

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center">Complete Your Payment</h2>
        <p class="text-center text-muted">Total Amount: <strong>$<?= number_format($total_price, 2); ?></strong></p>

        <div class="text-center">
            <form action="payment_process.php" method="post">
                <input type="hidden" name="total_price" value="<?= $total_price; ?>">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="pk_test_51Pmckg2MQuIXYxNU34LEYYSnfRPkinLSs4rS4CDLiOuIJ3JT4XmpAS0HEjtRMpfuDtCPjfjztfic8bAfDO2RfeK900UbJcwfkT"
                    data-amount="<?= $total_price * 100; ?>" data-name="Art Gallery"
                    data-description="Checkout for multiple items" data-currency="usd" data-email="<?= $email; ?>">
                </script>
            </form>
        </div>
    </div>
</div>

</body>

</html>