<?php
include 'header.php';

$error_message = $_SESSION['payment_error'] ?? "Payment could not be processed.";
unset($_SESSION['payment_error']);
?>
<div class="container mt-5 border rounded shadow text-center">
    <h2>Payment Failed!</h2>
    <p><?php echo htmlspecialchars($error_message); ?></p>
    <a href="checkout.php" class="btn btn-danger">Try Again</a>
</div>