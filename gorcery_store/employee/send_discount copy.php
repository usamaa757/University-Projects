<?php
include('../db_connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'vendor/autoload.php';
$customer_id = isset($_GET['customer_id']);
$customer_id = $_GET['customer_id'];
// Fetch all subscribed customers
$query = "SELECT name, email FROM customers WHERE subscription_status = 'subscribed' AND customer_id = '$customer_id'";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    while ($row = mysqli_fetch_assoc($result)) {
        // Send email to each subscribed customer
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
            $mail->addAddress($row['email'], $row['name']);

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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Discount Deals - XYZ Grocery Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Send Discount Deals to Subscribed Customers</h2>
        <form method="POST" action="send_discount.php?customer_id=<?php echo $customer_id;?>">
            <div class="form-group">
                <label for="subject">Email Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Email Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Discount Deals</button>
        </form>
    </div>
</body>

</html>