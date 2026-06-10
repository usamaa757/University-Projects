<?php
// Include necessary files
include 'header.php';  // Include header
include '../db_connection.php';  // Include database connection

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Check if the plant_id and quantity are passed via GET request for a new order
if (isset($_GET['plant_id']) && isset($_GET['quantity'])) {
    $plant_id = (int) $_GET['plant_id'];
    $quantity = (int) $_GET['quantity'];

    // Fetch the plant details
    $plant_query = "SELECT * FROM plants WHERE plant_id = ?";
    $stmt = $conn->prepare($plant_query);
    $stmt->bind_param('i', $plant_id);
    $stmt->execute();
    $plant_result = $stmt->get_result();

    if ($plant_result->num_rows > 0) {
        $plant = $plant_result->fetch_assoc();

        // Fetch the user's details
        $user_query = "SELECT * FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param('i', $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
        } else {
            echo "User not found.";
            exit;
        }

        // Check if there is enough stock available
        if ($plant['quantity'] >= $quantity) {
            // Total price calculation
            $total_price = $plant['price'] * $quantity;

            // Store order details in session
            $_SESSION['order'] = [
                'plant' => $plant,
                'user' => $user,
                'quantity' => $quantity,
                'total_price' => $total_price
            ];
        } else {
            echo "<p>Not enough stock available for the quantity you selected.</p>";
            exit;
        }
    } else {
        echo "Plant not found.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store additional order details in the session
    $_SESSION['order']['payment_method'] = $_POST['payment_method'];
    $_SESSION['order']['ship_address'] = $_POST['ship_address'];

    if ($_POST['payment_method'] == 'online') {
        // Redirect to the online payment page
        header('Location: online_pay.php');
        exit();
    } else {


        $order = $_SESSION['order'];

        // Validate session keys
        if (!isset($order['plant']['plant_id'], $order['user']['user_id'], $order['total_price'], $order['quantity'], $order['ship_address'])) {
            die("Incomplete order details. Please try again.");
        }

        $user_id = $order['user']['user_id'];
        $plant_id = $order['plant']['plant_id'];
        $quantity = $order['quantity'];
        $total_price = $order['total_price'];
        $ship_address = $order['ship_address'];

        // Insert the order into the database
        $insert_order_query = "
            INSERT INTO orders (user_id, plant_id, quantity, total_price, ship_address, payment_method, order_date)
            VALUES (?, ?, ?, ?, ?, 'COD', NOW())
        ";

        $order_stmt = $conn->prepare($insert_order_query);
        $order_stmt->bind_param('iiiss', $user_id, $plant_id, $quantity, $total_price, $ship_address);

        if ($order_stmt->execute()) {
            // Reduce the plant quantity in the database
            $plant_query = "UPDATE plants SET quantity = quantity - ? WHERE plant_id = ?";
            $stmt = $conn->prepare($plant_query);
            $stmt->bind_param('ii', $quantity, $plant_id);
            $stmt->execute();

            // Remove the item from the cart
            $remove_cart_item_query = "DELETE FROM cart WHERE user_id = ? AND plant_id = ?";
            $remove_cart_stmt = $conn->prepare($remove_cart_item_query);
            $remove_cart_stmt->bind_param('ii', $user_id, $plant_id);
            $remove_cart_stmt->execute();

            // Clear the order session
            unset($_SESSION['order']);

            // Show success message
            echo "<div class='container mt-5 text-center'>";
            echo "<h3>Your order has been saved for Cash on Delivery!</h3>";
            echo "<p><a href='order_details.php' class='btn btn-primary'>View Your Order Details</a></p>";
            echo "</div>";
        } else {
            echo "<div class='container mt-5 text-center'>";
            echo "<h3>Error</h3>";
            echo "<p>There was an issue processing your order. Please try again.</p>";
            echo "<a href='cart.php' class='btn btn-primary'>Return to Cart</a>";
            echo "</div>";
        }
    }
}


// Close the connection
mysqli_close($conn);
?>

<div class="container mt-5">
    <?php
    // Check if order session exists
    if (isset($_SESSION['order'])) {

        $order = $_SESSION['order'];
        $plant = $order['plant'];
        $user = $order['user'];
        $quantity = $order['quantity'];
        $total_price = $order['total_price'];

        // Order Summary Section
        echo "<div class='card shadow-lg p-4 mb-5'>";
        echo "<h3 class='mb-4 text-center'>Confirm Your Order</h3>";
        echo "<table class='table table-striped'>";
        echo "<tr><th>Plant Name</th><td>" . htmlspecialchars($plant['plant_name']) . "</td></tr>";
        echo "<tr><th>Type</th><td>" . htmlspecialchars($plant['plant_type']) . "</td></tr>";
        echo "<tr><th>Price per Item</th><td>Rs " . htmlspecialchars($plant['price']) . "</td></tr>";
        echo "<tr><th>Quantity</th><td>" . $quantity . "</td></tr>";
        echo "<tr><th>Total Price</th><td>Rs " . $total_price . "</td></tr>";
        echo "</table>";

        // User Info Section
        echo "<h5 class='mt-4'>Your Information</h5>";
        echo "<table class='table table-striped'>";
        echo "<tr><th>Name</th><td>" . htmlspecialchars($user['user_name']) . "</td></tr>";
        echo "<tr><th>Email</th><td>" . htmlspecialchars($user['email']) . "</td></tr>";
        echo "<tr><th>Phone</th><td>" . htmlspecialchars($user['phone']) . "</td></tr>";
        echo "<form action='purchase.php' method='POST'>";
        echo "<tr><th>Shipment Address</th><td><input type='text' id='ship_address' name='ship_address' value='" . htmlspecialchars($user["location"]) . "' class='form-control'></td></tr>";
        echo "</table>";

        // Payment Method Section
        echo "<h5 class='mt-4'>Payment</h5>";
        echo "<input type='hidden' name='plant_id' value='" . htmlspecialchars($plant['plant_id']) . "'>";
        echo "<input type='hidden' name='quantity' value='" . $quantity . "'>";
        echo "<input type='hidden' name='total_price' value='" . $total_price . "'>";

        echo "<div class='form-group'>
            <label for='paymentMethod'>Choose Payment Method</label>
            <select class='form-control' id='paymentMethod' name='payment_method' required>
                <option value='online'>Online Payment</option>
                <option value='cod'>Cash on Delivery</option>
            </select>
        </div>";
        echo "<div class='text-center'>
        <button type='submit' class='btn btn-success mt-4'>Confirm Purchase</button>
        </div>";

        echo "</form>";
        echo "</div>";
    }

    ?>
</div>

</body>

</html>