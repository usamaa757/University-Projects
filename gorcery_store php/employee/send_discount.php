<?php
include 'header.php';
include '../db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'vendor/autoload.php';

$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$error = '';
$user_info = [];

// Fetch subscribed customer details for the given customer ID
$query = "SELECT * FROM customers WHERE subscription_status = 'subscribed' AND customer_id = '$customer_id'";
$result = mysqli_query($conn, $query);

// Check if any records were found
if (mysqli_num_rows($result) > 0) {
    $user_info = mysqli_fetch_assoc($result); // Fetch the user details
} else {
    $error = "No subscribed customer found for the given Customer ID.";
}

// Handle email sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($user_info)) {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'louci786@gmail.com';  // SMTP username
        $mail->Password = 'pxcb potm zdli pypy';  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('louci786@gmail.com', 'XYZ Grocery Store');
        $mail->addAddress($user_info['email'], $user_info['name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        echo "<script>alert('Discount deals sent successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>

<div class="container mt-5 rounded shadow border p-0" style="max-width: 700px;">
    <h3 class="p-1 bg-dark text-white text-center">Discount Deals to Subscribed Customers</h3>
    <div class="p-3">

        <!-- Display error if no customer found -->
        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php else: ?>
        <!-- Display customer info -->
        <div class="mb-3">
            <h5>Customer Details</h5>
            <ul class="list-group">
                <li class="list-group-item"><strong>Name:</strong> <?php echo $user_info['name']; ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?php echo $user_info['email']; ?></li>
                <li class="list-group-item"><strong>Phone:</strong> <?php echo $user_info['phone']; ?></li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Email form -->
        <form method="POST" action="send_discount.php?customer_id=<?php echo $customer_id; ?>">
            <div class="form-group">
                <label for="subject">Email Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Email Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Send Discount Deals</button>
            </div>
        </form>
    </div>
</div>
</body>

</html>