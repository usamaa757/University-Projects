<?php
include 'header.php';
require 'vendor/autoload.php';
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Fetch quick stats
$user_id = $_SESSION['user_id'];

$publish_key = 'pk_test_51Pmckg2MQuIXYxNU34LEYYSnfRPkinLSs4rS4CDLiOuIJ3JT4XmpAS0HEjtRMpfuDtCPjfjztfic8bAfDO2RfeK900UbJcwfkT';
$secret_key = 'sk_test_51Pmckg2MQuIXYxNUn879nc35PxpQ9wtWcIAC6G9bdzrir6ukueG3O7nI8hqkghlKjYlTQWfBLBSwXtllJnU2NYn700Qa9eKRb9';

// Replace with your Stripe Secret Key
\Stripe\Stripe::setApiKey($secret_key);

// Check if seed ID is provided
if (!isset($_GET['id'])) {
    header("Location: seeds.php");
    exit();
}

$seed_id = intval($_GET['id']);
$sql = "SELECT * FROM seeds WHERE seed_id = $seed_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Seed not found.");
}
$seed = $result->fetch_assoc();

if (isset($_POST['checkout'])) {
    $quantity = intval($_POST['quantity']);
    $price_per_kg = floatval($seed['price_per_kg']);
    $total_price = $price_per_kg * $quantity;

    // Create Stripe Checkout session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'pkr',
                'product_data' => [
                    'name' => $seed['seed_name'],
                    'description' => $seed['description'],
                ],
                'unit_amount' => $price_per_kg * 100, // convert to paisa
            ],
            'quantity' => $quantity,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/seed_portal/success.php?seed_id=' . $seed_id . '&quantity=' . $quantity . '&total_price=' . $total_price . '&seed_name=' . $seed['seed_name'],
        'cancel_url' => 'http://localhost/seed_portal/cancel.php',
    ]);

    header("Location: " . $checkout_session->url);
    exit();
}
?>
<div class="container">

    <h2>🛒 Order Seed</h2>

    <div class="seed-order">
        <h3><?php echo htmlspecialchars($seed['seed_name']); ?></h3>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($seed['category']); ?></p>
        <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($seed['price_per_kg']); ?> per kg</p>
        <p><strong>Available:</strong> <?php echo htmlspecialchars($seed['quantity_available']); ?> kg</p>

        <form method="POST">
            <label for="quantity">Enter Quantity (kg):</label>
            <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $seed['quantity_available']; ?>"
                required>

            <button type="submit" name="checkout" class="btn">Proceed to Payment 💳</button>
        </form>
    </div>
</div>

</body>

</html>