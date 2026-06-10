<?php
include("../db_connection.php");
include("header.php");

// Assuming you have already set your API key and fetched order details as shown before

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    // Fetch order details from the database
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        $total_price = $order['total'];
        $currency = 'usd'; // Currency

        // Render the payment page with Bootstrap
        ?>
 
    <style>
        .form-control {
            border-radius: .25rem;
        }
    </style>

    <div class="container col-md-6 mt-5 p-0 border rounded">
        <h3 class="mb-4 bg-dark text-white p-2 text-center">Confirm Payment</h3>
        <div class="row justify-content-center p-3">
            <div class="col-md">
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                    <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
                    
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 1234 5678" pattern="\d{4} \d{4} \d{4} \d{4}" maxlength="19" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date (MM/YY)</label>
                        <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY" pattern="\d{2}/\d{2}" maxlength="5" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cvc">CVC</label>
                        <input type="text" class="form-control" id="cvc" name="cvc" placeholder="123" pattern="\d{3}" maxlength="3" required>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
        <?php
    } else {
        echo "Order not found.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
