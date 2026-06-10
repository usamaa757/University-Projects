<?php
include '../db.php';
include 'header.php';
$userID = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $propertyId = (int) $_GET['property_id'];
    $balance = (int) $_GET['balance'];
    $tenure = (int) $_GET['tenure'];
    $rate = (float) $_GET['rate'];
    $installment = (int) $_GET['installment'];
    $total = (int) $_GET['total'];

    $paid_installments = 1;

    $stmt = $conn->prepare("INSERT INTO purchases (property_id, user_id, remaining_balance, period_years, markup_rate, monthly_installment, paid_installments, total_payment) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiidiii", $propertyId, $userID, $balance, $tenure, $rate, $installment, $paid_installments, $total);

    $success = $stmt->execute();

    if ($success) {
        // ✅ Update property status to 'Rent'
        $updateStatus = $conn->prepare("UPDATE properties SET status = 'Sold' WHERE id = ?");
        $updateStatus->bind_param("i", $propertyId);
        $updateStatus->execute();
        $updateStatus->close();
    }
    $stmt->close();
}

?>


<?php if ($success): ?>
<div class="purchase-card">
    <div class="section-header">

        <h2> Purchase Successful</h2>
    </div>
    <p class="alert success">Thank you! Your installment plan has been confirmed.</p>
    <div class="details">
        <p><strong>Property ID:</strong> <?= $propertyId ?></p>
        <p><strong>Remaining Balance:</strong> Rs <?= number_format($balance) ?></p>
        <p><strong>Tenure:</strong> <?= $tenure ?> years</p>
        <p><strong>Markup Rate:</strong> <?= $rate ?>%</p>
        <p><strong>Monthly Installment:</strong> Rs <?= number_format($installment) ?></p>
        <p><strong>Total Payable:</strong> Rs <?= number_format($total) ?></p>
    </div><br>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
    <?php else: ?>
    <h2> Purchase Failed</h2>
    <p class="alert error">Sorry, we couldn't record your payment. Please try again later.</p>
    <a href="buy.php?id=<?= $propertyId ?>" class="btn">Try Again</a>
</div>
<?php endif; ?>
<?php include '../footer.php'; ?>

</body>

</html>