<?php
include 'header.php';
// Include database connection
include '../db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'vendor/autoload.php';
// Fetch admin data
$admin_id = $_SESSION['admin_id'];
$orders_query = "
SELECT o.*, c.category_name, u.user_name, u.email AS user_email, u.phone AS user_phone
FROM orders o
JOIN cloths p ON o.cloth_id = p.cloth_id
JOIN users u ON o.user_id = u.user_id
JOIN categories c ON p.category_id = c.category_id
";
$orders_result = mysqli_query($conn, $orders_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Update the order status in the database
    $update_query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $order_status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Order status updated successfully!";

        // Fetch the user's email
        $email_query = "SELECT * FROM users u JOIN orders o ON u.user_id = o.user_id WHERE o.order_id = ?";
        $email_stmt = $conn->prepare($email_query);
        $email_stmt->bind_param('i', $order_id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        $user_email = $email_result->fetch_assoc()['email'];

        // Set up PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'sajjalnoor381@gmail.com';  // SMTP username
            $mail->Password = 'fdop gtqr ekxr hgwi';  // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('sajjalnoor381@gmail.com', 'Thread & Clothing Trend');
            $mail->addAddress($user_email);
            // Content
            $mail->isHTML(true);                                           // Set email format to HTML
            $mail->Subject = "Order Status Update - " . $order_status;
            $message = "";

            $order_details_query = "
            SELECT 
                o.order_id, 
                o.quantity, 
                p.price, 
                c.category_name 
            FROM orders o 
            JOIN cloths p ON o.cloth_id = p.cloth_id 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE o.order_id = ?";

            $details_stmt = $conn->prepare($order_details_query);
            $details_stmt->bind_param('i', $order_id);
            $details_stmt->execute();
            $details_result = $details_stmt->get_result();

            // Start building the email message
            $message .= "Dear Customer,<br><br>";
            $message .= "Your order #$order_id status has been updated to '$order_status'.<br><br>";
            $message .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
            $message .= "<thead><tr><th>Type</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead>";
            $message .= "<tbody>";

            $total_amount = 0; // Initialize total amount
            while ($row = $details_result->fetch_assoc()) {
                $item_name = $row['category_name'];
                $quantity = $row['quantity'];
                $price = $row['price'];
                $total = $quantity * $price;
                $total_amount += $total;

                // Display each item in the order
                $message .= "<tr>";
                $message .= "<td>$item_name</td><td>$quantity</td><td>" . number_format($price, 2) . "</td><td>" . number_format($total, 2) . "</td>";
                $message .= "</tr>";
            }

            $message .= "</tbody>";
            $message .= "</table><br><br>";
            $message .= "<b>Total Order Amount: " . number_format($total_amount, 2) . "</b><br><br>";

            // Customize the email message based on order status
            if ($order_status === 'Shipped') {
                $message .= "Your order has been shipped.<br><br>Thank you for shopping with us.";
            } elseif ($order_status === 'Completed') {
                $message .= "Your order has been completed and delivered. Thank you for your purchase!";
            }

            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            $_SESSION['success'] .= " An email notification has been sent to the customer.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Error updating order status.";
    }
    $stmt->close();
    mysqli_close($conn);

    // Redirect back to the orders page
    header("Location: orders.php");
    exit();
}

?>
<div class="container mt-5 round border shadow p-3">
    <h3 class="mb-4 text-center">Manage Orders for Your Cloths</h3>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
    <?php unset($_SESSION['success']);
    endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
    <?php unset($_SESSION['error']);
    endif; ?>

    <!-- Orders section -->
    <div class="row">
        <?php
        if (mysqli_num_rows($orders_result) > 0) {
            while ($order = mysqli_fetch_assoc($orders_result)) {
        ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0">Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h4>
                        </div>
                        <div class="card-body">
                            <p><strong>User Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                            <p><strong>User Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                            <p><strong>User Phone:</strong> <?php echo htmlspecialchars($order['user_phone']); ?></p>
                            <p><strong>Cloth Type:</strong> <?php echo htmlspecialchars($order['category_name']); ?></p>
                            <p><strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
                            <p><strong>Total Price:</strong> Rs <?php echo htmlspecialchars($order['total_price']); ?></p>
                            <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?></p>
                            <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['ship_address']); ?></p>
                            <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                            <?php if ($order['order_status'] == 'Completed') {
                                echo "<h5 class='text-center text-success'>Order completed</h5>";
                            } else {
                            ?>
                                <!-- Order status update form -->
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="order_status_<?php echo $order['order_id']; ?>">Update Order Status</label>
                                        <select class="form-control" id="order_status_<?php echo $order['order_id']; ?>"
                                            name="order_status" required>

                                            <option value="" disabled selected>Select</option>
                                            <option value="Shipped"
                                                <?php echo ($order['order_status'] == 'Shipped') ? 'selected' : ''; ?>>
                                                Shipped
                                            </option>
                                            <option value="Completed"
                                                <?php echo ($order['order_status'] == 'Completed') ? 'selected' : ''; ?>>
                                                Completed
                                            </option>

                                        </select>
                                    </div>
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <div class="text-center">
                                        <button type="submit" class="btn text-white bg-primary mt-3">Update Status</button>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<div class='col-12 alert alert-info text-center'>No orders found.</div>";
        }
        ?>
    </div>
</div>