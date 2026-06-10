<?php
// Include necessary files
include 'header.php';
include '../db_connection.php';

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Check if the cloth_id and quantity are passed via GET request for a new order
if (isset($_GET['cloth_id']) && isset($_GET['quantity'])) {
    $cloth_id = (int) $_GET['cloth_id'];
    $quantity = (int) $_GET['quantity'];

    // Fetch the cloth details
    $cloth_query = "
    SELECT 
        c.*, 
        cat.category_name 
    FROM 
        cloths c
    JOIN 
        categories cat 
    ON 
        c.category_id = cat.category_id
    WHERE 
        c.cloth_id = ?
    ";

    $stmt = $conn->prepare($cloth_query);
    $stmt->bind_param('i', $cloth_id);
    $stmt->execute();
    $cloth_result = $stmt->get_result();

    if ($cloth_result->num_rows > 0) {
        $cloth = $cloth_result->fetch_assoc();

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
        if ($cloth['quantity'] >= $quantity) {
            // Total price calculation
            $total_price = $cloth['price'] * $quantity;

            // Store order details in session
            $_SESSION['order'] = [
                'cloth' => $cloth,
                'user' => $user,
                'quantity' => $quantity,
                'total_price' => $total_price
            ];
        } else {
            echo "<p>Not enough stock available for the quantity you selected.</p>";
            exit;
        }
    } else {
        echo "Cloth not found.";
        exit;
    }
}

// Handle form submission for confirming the purchase
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store additional order details in the session
    $_SESSION['order']['ship_address'] = $_POST['ship_address'];

    // Redirect to the online payment page
    header('Location: online_pay.php');
    exit;
}

// Close the connection
mysqli_close($conn);
?>

<!-- HTML for Order Summary -->
<div class="container mt-5">
    <?php
    // Check if order session exists
    if (isset($_SESSION['order'])) {
        $order = $_SESSION['order'];
        $cloth = $order['cloth'];
        $user = $order['user'];
        $quantity = $order['quantity'];
        $total_price = $order['total_price'];

        // Order Summary Section
        echo "<div class='card shadow-lg p-4 mb-5'>";
        echo "<h3 class='mb-4 text-center'>Confirm Your Order</h3>";
        echo "<table class='table table-striped'>";
        echo "<tr><th>Type</th><td>" . htmlspecialchars($cloth['category_name']) . "</td></tr>";
        echo "<tr><th>Price per Item</th><td>Rs " . htmlspecialchars($cloth['price']) . "</td></tr>";
        echo "<tr><th>Quantity</th><td>" . $quantity . "</td></tr>";
        echo "<tr><th>Total Price</th><td>Rs " . $total_price . "</td></tr>";
        echo "</table>";

        // User Info Section
        echo "<h5 class='mt-4'>Your Information</h5>";
        echo "<table class='table table-striped'>";
        echo "<tr><th>Name</th><td>" . htmlspecialchars($user['user_name']) . "</td></tr>";
        echo "<tr><th>Email</th><td>" . htmlspecialchars($user['email']) . "</td></tr>";
        echo "<tr><th>Phone</th><td>" . htmlspecialchars($user['phone']) . "</td></tr>";
        echo "<form action='' method='POST'>";
        echo "<tr><th>Shipment Address</th><td><input type='text' id='ship_address' name='ship_address' value='" . htmlspecialchars($user["location"]) . "' class='form-control'></td></tr>";
        echo "</table>";

        // Payment Method Section
        echo "<input type='hidden' name='cloth_id' value='" . htmlspecialchars($cloth['cloth_id']) . "'>";
        echo "<input type='hidden' name='quantity' value='" . $quantity . "'>";
        echo "<input type='hidden' name='total_price' value='" . $total_price . "'>";

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