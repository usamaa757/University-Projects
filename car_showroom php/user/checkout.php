<?php
include '../db.php';
include 'header.php';

if (!isset($_SESSION['cart'])) {
    echo "<script>alert('Cart is empty!'); window.location.href='car_list.php';</script>";
    exit;
}

$publish_key = 'pk_test_51RGjKECa2iCGPbLUWqDvF3QdMSB2c6b1V4hA0Lvdmyb5Iz6znKNEVh8xcpQ810EeMxc9inkTEgkQ5EVbFR6i1tne00Uo5Dq7FK';
$cart = $_SESSION['cart'];
$car_id = $cart['car_id'];

// Fetch car
$carQuery = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
$carQuery->bind_param("i", $car_id);
$carQuery->execute();
$car = $carQuery->get_result()->fetch_assoc();

// Fetch city
$cityQuery = $conn->prepare("SELECT city_name, delivery_charge FROM cities WHERE city_id = ?");
$cityQuery->bind_param("i", $cart['city_id']);
$cityQuery->execute();
$city = $cityQuery->get_result()->fetch_assoc();

$delivery_charge = $city['delivery_charge'];
$total_amount = $car['price'] + $delivery_charge;
$installment_plan = $cart['installment_plan'];
$initial_payment_percentage = 0.20;
$monthly_amount = $installment_plan ? $total_amount / $installment_plan : 0;

if ($installment_plan) {
    $stripe_amount = $total_amount * $initial_payment_percentage;
    $payment_label = "Initial Payment (20%)";
} else {
    $stripe_amount = $total_amount;
    $payment_label = "Full Payment";
}
?>



<div class="container my-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">Checkout - Confirm Your Order</h2>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Car:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($car['model']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Name:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['name']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>City:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($city['city_name']) ?> (Rs. <?= $delivery_charge ?> delivery)
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Total:</strong></div>
            <div class="col-md-6">Rs. <?= number_format($total_amount, 2) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Payment Mode:</strong></div>
            <div class="col-md-6"><?= $payment_label ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Amount to Pay Now:</strong></div>
            <div class="col-md-6">Rs. <?= number_format($stripe_amount, 2) ?></div>
        </div>

        <?php if ($installment_plan): ?>
        <div class="row mb-3">
            <div class="col-md-6"><strong>Installment Plan:</strong></div>
            <div class="col-md-6"><?= $installment_plan ?> months × Rs. <?= number_format($monthly_amount, 2) ?></div>
        </div>
        <?php endif; ?>

        <form action="process_payment.php" method="POST">
            <div class="d-flex justify-content-center">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="<?= $publish_key ?>" data-amount="<?= intval($stripe_amount * 100) ?>"
                    data-name="Car Purchase" data-description="<?= $payment_label ?> - <?= $car['model'] ?>"
                    data-image="<?= '../' . $car['image'] ?>" data-locale="auto" data-currency="Pkr"
                    data-label="Proceed to Payment">
                </script>
            </div>
        </form>
    </div>
</div>
</body>

</html>