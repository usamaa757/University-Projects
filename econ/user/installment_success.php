<?php
require '../db.php';
include 'header.php';

$purchaseId = (int)$_GET['purchase_id'];
$installment = (int)$_GET['installment'];

// Update the purchase
$stmt = $conn->prepare("UPDATE purchases 
    SET remaining_balance = remaining_balance - ?, 
        paid_installments = paid_installments + 1 
    WHERE id = ?");
$stmt->bind_param("ii", $installment, $purchaseId);
$stmt->execute();
$stmt->close();

// Fetch updated details
$stmt = $conn->prepare("SELECT p.remaining_balance, p.paid_installments, pr.title 
    FROM purchases p 
    JOIN properties pr ON p.property_id = pr.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $purchaseId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>
<div class="installment-box">
    <div class="confirmation-box">
        <div class="checkmark"></div>
        <div class="section-header">

            <h2>Installment Paid Successfully!</h2>
        </div>
        <div class="info">
            <p><span>Purchase ID:</span> <?= htmlspecialchars($purchaseId) ?></p>
            <p><span>Property:</span> <?= htmlspecialchars($data['title']) ?></p>
            <p><span>Installment Paid:</span> $ <?= number_format($installment) ?></p>
            <p><span>Paid Installments:</span> <?= htmlspecialchars($data['paid_installments']) ?></p>
            <p><span>Remaining Balance:</span> $ <?= number_format($data['remaining_balance']) ?></p>
            <p><span>Date:</span> <?= date('F j, Y, g:i a') ?></p>
        </div>
        <a href="installments.php" class="btn">Back to Installments</a>
    </div>
</div>
<?php include '../footer.php'; ?>

</body>

</html>