<?php
include '../db.php';
include 'header.php';

if (!isset($_SESSION['cart'])) {
    echo "<script>alert('Cart is empty!'); window.location.href='art_list.php';</script>";
    exit;
}

$publish_key = 'pk_test_51RGjKECa2iCGPbLUWqDvF3QdMSB2c6b1V4hA0Lvdmyb5Iz6znKNEVh8xcpQ810EeMxc9inkTEgkQ5EVbFR6i1tne00Uo5Dq7FK';
$cart = $_SESSION['cart'];
$art_id = $cart['art_id'];


?>
<div class="container my-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">Checkout - Confirm Your Art Purchase</h2>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Artwork:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['art_name']) ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><strong>Art Price:</strong></div>
            <div class="col-md-6">Rs. <?= number_format($cart['price'], 2) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Customer Name:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['name']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Email:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['email']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Phone:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['phone']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Address:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['address']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Payment Method:</strong></div>
            <div class="col-md-6"><?= htmlspecialchars($cart['payment_method']) ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><strong>Total Amount:</strong></div>
            <div class="col-md-6">Rs. <?= number_format($cart['total_amount'], 2) ?></div>
        </div>

        <form action="process_payment.php" method="POST">
            <div class="d-flex justify-content-center">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="<?= $publish_key ?>" data-amount="<?= intval($cart['total_amount'] * 100) ?>"
                    data-name="Artwork Purchase" data-description="<?= htmlspecialchars($cart['art_name']) ?>"
                    data-image="<?= $cart['image'] ?>" data-locale="auto" data-currency="pkr"
                    data-label="Proceed to Payment">
                </script>
            </div>
        </form>
    </div>
</div>
</body>

</html>