<?php
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);
$msg = $error = '';

// Fetch order info along with complaints (if any)
$order_result = mysqli_query($conn, "
    SELECT o.*, f.name AS furniture_name, u.name AS seller_name, c.id AS complaint_id, c.subject, c.message, 
           c.reply, c.status, c.created_at, c.replied_at
    FROM orders o
    JOIN furniture f ON o.furniture_id = f.id
    JOIN users u ON o.seller_id = u.id
    LEFT JOIN complaints c ON o.id = c.order_id AND c.buyer_id = '$buyer_id'
    WHERE o.id='$order_id' AND o.buyer_id='$buyer_id'
");
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    die("<p>Invalid order or you do not have permission to view this order.</p>");
}

// Handle form submission
if (isset($_POST['submit_complaint'])) {
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    $seller_id = $order['seller_id'];

    if (empty($subject) || empty($message)) {
        $error = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO complaints (order_id, buyer_id, seller_id, subject, message, status, created_at) 
                VALUES ('$order_id', '$buyer_id', '$seller_id', '$subject', '$message', 'Pending', NOW())";
        if (mysqli_query($conn, $sql)) {
            $msg = "Complaint submitted successfully!";
            // Refresh complaint info
            $order_result = mysqli_query($conn, "
                SELECT o.*, f.name AS furniture_name, u.name AS seller_name, c.id AS complaint_id, c.subject, c.message, 
                       c.reply, c.status, c.created_at, c.replied_at
                FROM orders o
                JOIN furniture f ON o.furniture_id = f.id
                JOIN users u ON o.seller_id = u.id
                LEFT JOIN complaints c ON o.id = c.order_id AND c.buyer_id = '$buyer_id'
                WHERE o.id='$order_id' AND o.buyer_id='$buyer_id'
            ");
            $order = mysqli_fetch_assoc($order_result);
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="form-container">
    <h2>Complaint for "<?php echo htmlspecialchars($order['furniture_name']); ?>"</h2>

    <?php if (!empty($msg)): ?>
    <div class="success-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>

    <?php if (!empty($order['complaint_id'])): ?>
    <div class="complaint-box">
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($order['subject']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($order['message']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
        <?php if (!empty($order['reply'])): ?>
        <p><strong>Seller Reply:</strong> <?php echo htmlspecialchars($order['reply']); ?></p>
        <p><small>Replied at: <?php echo date("d M Y H:i", strtotime($order['replied_at'])); ?></small></p>
        <?php else: ?>
        <p><em>Not replied yet.</em></p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <form method="POST">
        <label for="subject">Subject:</label>
        <input type="text" name="subject" placeholder="Complaint subject" required>

        <label for="message">Message:</label>
        <textarea name="message" placeholder="Write your complaint here..." required></textarea>

        <button type="submit" name="submit_complaint">Submit Complaint</button>
    </form>
    <?php endif; ?>

    <p><a href="purchased_history.php">Back to Purchased History</a></p>
</div>