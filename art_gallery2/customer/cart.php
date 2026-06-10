<?php
include 'header.php';
include '../db.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo '<div class="container mt-5 text-center rounded shadow border p-3">
            <h2 class="text-danger">Your cart is empty.</h2>
            <a href="art_list.php" class="btn btn-primary mt-3">Go Shopping</a>
          </div>';
    exit();
}

// Fetch artworks from the database
$ids = implode(",", array_keys($_SESSION['cart']));
$result = $conn->query("SELECT * FROM art_items WHERE art_id IN ($ids)");
?>

<div class="container mt-5 border rounded shadow">
    <h2 class="text-center mb-4">Your Cart</h2>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Artwork</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                while ($row = $result->fetch_assoc()) {
                    $art_id = $row['art_id'];
                    $quantity = $_SESSION['cart'][$art_id];
                    $subtotal = $row['price'] * $quantity;
                    $total_price += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['art_name']); ?></td>
                    <td>$<?= number_format($row['price'], 2); ?></td>
                    <td><?= $quantity; ?></td>
                    <td>$<?= number_format($subtotal, 2); ?></td>
                    <td>
                        <a href="remove_from_cart.php?art_id=<?= $row['art_id']; ?>" class="btn btn-danger btn-sm">
                            Remove
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <h3 class="text-center mt-4">Total: $<?= number_format($total_price, 2); ?></h3>

    <div class="text-center my-4">
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        <a href="art_list.php" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>

</body>

</html>