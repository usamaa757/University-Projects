<?php
include("header.php");
include("../db_connection.php");

if (isset($_GET['part_id'])) {
    $part_id = intval($_GET['part_id']);

    // Fetch part details
    $stmt = $conn->prepare("SELECT * FROM auto_parts WHERE part_id = ?");
    $stmt->bind_param("i", $part_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $part = $result->fetch_assoc();
    
    if ($part) {
        // Process the purchase
        $buyer_id = $_SESSION['buyer_id']; // Assume user is logged in
        $total_price = $part['price'];
        $part_name = $part['part_name'];
        $part_id = $part['part_id'];
        $model = $part['model'];
        $make = $part['make'];
   
        // Redirect to the success page
        header("Location: place_order.php?part_id=" . urlencode($part_name) . "&part_name=" . urlencode($part_name) . "&model=" . urlencode($model) . "&make=" . urlencode($make) . "&total_price=" . urlencode($total_price));
        exit();
    } 
}

?>

    <div class="container mt-3">
        <h1 class="mb-4">Order Successful</h1>
        <div class="alert alert-success">
            <h4 class="alert-heading">Thank you for your purchase!</h4>
            <p>Your order has been successfully placed. Below are the details of your order:</p>
            <hr>
            <table class="table table-bordered">
                
                <tr>
                    <th>Part Name</th>
                    <td><?php echo htmlspecialchars($_GET['part_name']); ?></td>
                </tr>
                <tr>
                    <th>Model</th>
                    <td><?php echo htmlspecialchars($_GET['model']); ?></td>
                </tr>
                <tr>
                    <th>Make</th>
                    <td><?php echo htmlspecialchars($_GET['make']); ?></td>
                </tr>
                <tr>
                    <th>Total Price</th>
                    <td>$<?php echo htmlspecialchars($_GET['total_price']); ?></td>
                </tr>
            </table>
            <hr>
            <form method="POST" action="cash_on_delivery.php?part_id=<?php echo $part_id =isset($_GET['part_id']); ?>">
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="online_payment">Online Payment</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Order</button>
        </form>
        </div>
        <a href="purchase_list.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</body>
</html>
