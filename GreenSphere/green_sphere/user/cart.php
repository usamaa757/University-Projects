<?php
include 'header.php';
include '../db_connection.php';

$user_id = $_SESSION['user_id'];

// Handle POST requests to update the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity']) && isset($_POST['plant_id'])) {
    $quantity = $_POST['quantity'];
    $plant_id = $_POST['plant_id'];

    // Validate the quantity
    if ($quantity <= 0) {
        $error_message = "Invalid quantity.";
    } else {
        // Check if the plant exists in the database
        $plant_query = "SELECT * FROM plants WHERE plant_id = ?";
        $stmt = $conn->prepare($plant_query);
        $stmt->bind_param('i', $plant_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $plant = $result->fetch_assoc();

            // Check if enough stock is available
            if ($plant['quantity'] >= $quantity) {

                // Check if the plant is already in the user's cart
                $cart_query = "SELECT quantity FROM cart WHERE user_id = ? AND plant_id = ?";
                $cart_stmt = $conn->prepare($cart_query);
                $cart_stmt->bind_param('ii', $user_id, $plant_id);
                $cart_stmt->execute();
                $cart_result = $cart_stmt->get_result();

                if ($cart_result->num_rows > 0) {
                    // If the plant is already in the cart, update the quantity
                    $cart = $cart_result->fetch_assoc();
                    $new_quantity = $quantity;

                    // Update the cart with the new quantity
                    $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND plant_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param('iii', $new_quantity, $user_id, $plant_id);
                    $update_stmt->execute();
                    $update_stmt->close();

                    $success_message = "Quantity successfully updated!";
                } else {
                    // If the plant is not in the cart, add it
                    $insert_query = "INSERT INTO cart (user_id, plant_id, quantity) VALUES (?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_query);
                    $insert_stmt->bind_param('iii', $user_id, $plant_id, $quantity);
                    $insert_stmt->execute();
                    $insert_stmt->close();

                    $success_message = "Plant successfully added to the cart!";
                }
            } else {
                $error_message = "Not enough stock available.";
            }
        } else {
            $error_message = "Plant not found.";
        }
    }
}

// Fetch current cart items
$cart_query = "SELECT ci.plant_id, p.plant_name, p.plant_type, p.quantity, p.price, p.image_url, SUM(ci.quantity) AS total_quantity
               FROM cart ci
               JOIN plants p ON ci.plant_id = p.plant_id
               WHERE ci.user_id = ?
               GROUP BY ci.plant_id";
$cart_stmt = $conn->prepare($cart_query);
$cart_stmt->bind_param('i', $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

?>

<div class="container mt-5 round border shadow p-3">
    <h3>Your Cart</h3>

    <?php

    if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
    <?php
        unset($_SESSION['message']); // Clear the message after displaying
    }
    if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($cart_result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Image</th>
                    <th>Plant Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($row = $cart_result->fetch_assoc()):
                    $total_price = $row['price'] * $row['total_quantity'];
                ?>
                    <tr>
                        <td><img src="../seller/<?php echo htmlspecialchars($row['image_url']); ?>" alt="Plant Image"
                                class="img-fluid" style="height:100px;"></td>
                        <td><?php echo htmlspecialchars($row['plant_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plant_type']); ?></td>
                        <td>Rs <?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <form action="cart.php" method="POST">
                                <input type="number" name="quantity"
                                    value="<?php echo htmlspecialchars($row['total_quantity']); ?>" min="1"
                                    max="<?php echo htmlspecialchars($row['quantity']); ?>" required class="form-control">
                                <input type="hidden" name="plant_id" value="<?php echo htmlspecialchars($row['plant_id']); ?>">
                                <button type="submit" class="btn btn-warning btn-sm mt-2">Update</button>
                            </form>
                        </td>
                        <td>Rs <?php echo $total_price; ?></td>
                        <td><a href="remove_from_cart.php?plant_id=<?php echo htmlspecialchars($row['plant_id']); ?>"
                                class="btn btn-danger btn-sm">Remove</a></td>
                        <td><a href="purchase.php?plant_id=<?php echo htmlspecialchars($row['plant_id']); ?>&quantity=<?php echo htmlspecialchars($row['total_quantity']); ?>"
                                class="btn btn-success btn-sm">Purchase</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

</div>

</body>

</html>

<?php
// Close the connection
mysqli_close($conn);
?>