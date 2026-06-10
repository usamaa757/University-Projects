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

// Fetch order info
$order_result = mysqli_query($conn, "SELECT o.*, f.name AS furniture_name, u.name AS seller_name
                                     FROM orders o
                                     JOIN furniture f ON o.furniture_id = f.id
                                     JOIN users u ON o.seller_id = u.id
                                     WHERE o.id='$order_id' AND o.buyer_id='$buyer_id'");
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    die("<p>Invalid order or you do not have permission to give feedback for this order.</p>");
}

// Handle form submission
if (isset($_POST['submit_feedback'])) {
    $rating = intval($_POST['rating']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $seller_id = $order['seller_id'];

    if ($rating < 1 || $rating > 5) {
        $error = "Please select a valid rating.";
    } else {
        $sql = "INSERT INTO feedback (order_id, buyer_id, seller_id, rating, comments) 
                VALUES ('$order_id', '$buyer_id', '$seller_id', '$rating', '$comments')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Feedback submitted successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="form-container">
    <h2>Give Feedback for "<?php echo htmlspecialchars($order['furniture_name']); ?>"</h2>

    <?php if (!empty($msg)): ?>
        <div class="success-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>

        <label for="rating">Rating:</label>
        <select name="rating" required>
            <option value="">--Select Rating--</option>
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Good</option>
            <option value="3">3 - Average</option>
            <option value="2">2 - Poor</option>
            <option value="1">1 - Very Poor</option>
        </select>

        <label for="comments">Comments (optional):</label>
        <textarea name="comments" placeholder="Write your feedback here..."></textarea>

        <button type="submit" name="submit_feedback">Submit Feedback</button>
    </form>

    <p><a href="purchased_history.php">Back to Purchased History</a></p>
</div>
