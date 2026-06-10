<?php

include 'header.php';
include '../db.php';

$customer_id = $_SESSION['customer_id'] ?? null;

if (!$customer_id) {
    echo "<p>Please log in to view your cart.</p>";
    exit;
}

// Fetch cart items, joining with products or services depending on what's in the cart
$query = "SELECT 
            c.cart_id, p.image_path,
            c.quantity,
            COALESCE(p.product_name, s.service_name) AS item_name,
            COALESCE(p.brand, s.description) AS brand,
            COALESCE(p.price, s.price) AS price,
            CASE 
                WHEN c.product_id IS NOT NULL THEN 'product'
                ELSE 'service'
            END AS item_type
          FROM cart c
          LEFT JOIN products p ON c.product_id = p.product_id
          LEFT JOIN services s ON c.service_id = s.service_id
          WHERE c.customer_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Your Cart</h2>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Type</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['brand']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= ucfirst($row['item_type']) ?></td>
                        <td>$<?= number_format($subtotal, 2) ?></td>
                        <td>
                            <img src=" <?= $row['image_path'] ?>" alt="Product Image" class="product-img">
                        </td>
                        <td>
                            <a href="remove_from_cart.php?cart_id=<?= $row['cart_id'] ?>"
                                onclick="return confirm('Are you sure you want to remove this item from your cart?');"
                                class="btn">Remove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="5"><strong>Total</strong></td>
                    <td><strong>$<?= number_format($total, 2) ?></strong></td>
                    <td></td>
                    <td><a href="checkout.php" class="btn">Checkout</a></td>
                </tr>

            </tbody>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>