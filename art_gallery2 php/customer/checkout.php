<?php

include 'header.php';
include '../db.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo '<div class="container mt-5 text-center rounded shadow border p-3">
            <h2 class="text-danger">Your cart is empty.</h2>
            <a href="art_list.php" class="btn btn-primary mt-3">Go Shopping</a>
          </div>';
    exit();
}

// Fetch cart items
$ids = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT a.*, u.username FROM art_items a 
        JOIN users u ON a.seller_id = u.user_id 
        WHERE art_id IN ($ids)";
$result = $conn->query($sql);

// Calculate total amount
$total = 0;
?>

<div class="container mt-5 border rounded shadow p-3">
    <h2 class="text-center mb-4">Checkout</h2>
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Artwork</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($artwork = $result->fetch_assoc()) {
                        $art_id = $artwork['art_id'];
                        $quantity = $_SESSION['cart'][$art_id];
                        $subtotal = $artwork['price'] * $quantity;
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($artwork['art_name']); ?> <br>
                            <small class="text-muted">By <?= htmlspecialchars($artwork['username']); ?></small>
                        </td>
                        <td>$<?= number_format($artwork['price'], 2); ?></td>
                        <td><?= $quantity; ?></td>
                        <td>$<?= number_format($subtotal, 2); ?></td>
                    </tr>

                    <input type="hidden" name="items[<?= $art_id; ?>][price]" value="<?= $artwork['price']; ?>">
                    <input type="hidden" name="items[<?= $art_id; ?>][quantity]" value="<?= $quantity; ?>">
                    <?php } ?>
                </tbody>
            </table>
            <h4 class="text-end">Total: <span class="text-success">$<?= number_format($total, 2); ?></span></h4>
        </div>

        <div class="col-md-4 border rounded p-3">
            <h4 class="text-center">Shipping Details</h4>
            <form action="payment.php" method="POST">
                <?php foreach ($_SESSION['cart'] as $art_id => $quantity): ?>
                <input type="hidden" name="art_ids[]" value="<?= $art_id ?>">
                <?php endforeach; ?>

                <?php foreach ($_SESSION['cart'] as $art_id => $quantity): ?>
                <input type="hidden" name="quantities[<?= $art_id ?>]" value="<?= $quantity ?>">
                <?php endforeach; ?>

                <input type="hidden" name="total_price" value="<?= $total; ?>">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required
                        value="<?= $_SESSION['username'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required
                        value="<?= $_SESSION['email'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <textarea name="address" class="form-control" required><?= $_SESSION['address'] ?? ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" required
                        value="<?= $_SESSION['phone'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="Online">Online Payment</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                    </select>
                </div>
                <div class="text-center mb-3">
                    <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                </div>

            </form>
        </div>
    </div>
</div>

</body>

</html>