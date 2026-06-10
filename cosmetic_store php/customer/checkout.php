<?php
include 'header.php';
include '../db.php';

$customer_id = $_SESSION['customer_id'];

// Fetch customer address and phone
$query = "SELECT phone_number, address FROM customer WHERE customer_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);

// Fetch cart items (only products)
$cart_query = "SELECT * FROM cart WHERE customer_id = ? AND quantity > 0 AND product_id IS NOT NULL";
$stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$cart_result = mysqli_stmt_get_result($stmt);

$cartItems = [];
$total = 0;

while ($row = mysqli_fetch_assoc($cart_result)) {
    $product_id = $row['product_id'];
    $quantity = $row['quantity'];

    // Fetch product details
    $price_stmt = mysqli_prepare($conn, "SELECT product_name, brand, price FROM products WHERE product_id = ?");
    mysqli_stmt_bind_param($price_stmt, "i", $product_id);
    mysqli_stmt_execute($price_stmt);
    $price_result = mysqli_stmt_get_result($price_stmt);
    $price_data = mysqli_fetch_assoc($price_result);

    $item = $row;
    $item['name'] = $price_data['product_name'];
    $item['brand'] = $price_data['brand'];
    $item['price'] = $price_data['price'];
    $item['subtotal'] = $item['price'] * $quantity;

    $total += $item['subtotal'];
    $cartItems[] = $item;
}

// If cart is empty, notify the user
if (empty($cartItems)) {
    echo "Your cart is empty.";
    exit;
}

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Insert into orders table with address, location, phone
    $order_query = "INSERT INTO orders (customer_id, total_price, address, phone_number) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, "idss", $customer_id, $total, $address, $phone);
    mysqli_stmt_execute($stmt);

    $order_id = mysqli_insert_id($conn);


    // Insert products into order_items table
    // Insert products into order_items table and decrease stock
    foreach ($cartItems as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        // Insert order item
        $item_insert = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $item_stmt = mysqli_prepare($conn, $item_insert);
        mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $product_id, $quantity, $price);
        mysqli_stmt_execute($item_stmt);

        // Decrease product quantity
        $update_product = "UPDATE products SET quantity = quantity - ? WHERE product_id = ? AND quantity >= ?";
        $update_stmt = mysqli_prepare($conn, $update_product);
        mysqli_stmt_bind_param($update_stmt, "iii", $quantity, $product_id, $quantity);
        mysqli_stmt_execute($update_stmt);
    }


    // Clear the cart
    $clear_cart = "DELETE FROM cart WHERE customer_id = ?";
    $stmt = mysqli_prepare($conn, $clear_cart);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);

    // Redirect to confirmation page
    echo "<script>alert('Your order has been placed successfully!'); window.location.href='order_confirmation.php?order_id=$order_id';</script>";
    exit;
}
?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Checkout</h2>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['brand']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <br>

    <form method="POST" class="form">
        <h3>Billing Information</h3>
        <br>
        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($customer['address']) ?>" required>
        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone_number']) ?>" required>

        <div class="btn-div">
            <button type="submit" class="btn">Place Order</button>
        </div>
    </form>
</div>

</body>

</html>