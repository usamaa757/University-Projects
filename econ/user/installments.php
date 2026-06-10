<?php
include '../db.php';
include 'header.php';



$userId = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// Fetch user purchases with property details
$sql = "SELECT 
            p.id AS purchase_id, 
            p.monthly_installment, 
            p.total_payment, 
            p.remaining_balance, 
            p.paid_installments, 
            p.period_years,
            pr.title 
        FROM purchases p 
        JOIN properties pr ON p.property_id = pr.id 
        WHERE p.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$purchases = [];
while ($row = $result->fetch_assoc()) {
    $purchases[] = $row;
}

$stmt->close();
?>

<div class="section-header">

    <h2> Make Your Installment Payments</h2>
</div>
<?php if (count($purchases) === 0): ?>
    <p style="text-align:center;">You have
        no purchases yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Property</th>
            <th>Period (Years)</th> <!-- New column -->
            <th>Total/Paid Installments</th> <!-- Renamed -->
            <th>Monthly Installment</th>
            <th>Total Payment</th>
            <th>Remaining Balance</th>
            <th>Action</th>
        </tr>
        <?php foreach ($purchases as $purchase): ?>
            <?php
            $totalInstallments = $purchase['period_years'] * 12;
            $paidInstallments = $purchase['paid_installments'];
            $isFullyPaid = $paidInstallments >= $totalInstallments;
            $progressPercent = min(100, round(($paidInstallments / $totalInstallments) * 100));
            ?>
            <tr>
                <td><?= htmlspecialchars($purchase['title']) ?></td>
                <td><?= $purchase['period_years'] ?></td>
                <td>
                    <?= $paidInstallments ?> / <?= $totalInstallments ?> installments
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?= $progressPercent ?>%;"></div>
                    </div>
                </td>
                <td>$ <?= number_format($purchase['monthly_installment']) ?></td>
                <td>$ <?= number_format($purchase['total_payment']) ?></td>
                <td>$ <?= number_format($purchase['remaining_balance']) ?></td>
                <td>
                    <form action="installment_pay.php" method="POST">
                        <input type="hidden" name="purchase_id" value="<?= $purchase['purchase_id'] ?>">
                        <input type="hidden" name="installment" value="<?= $purchase['monthly_installment'] ?>">
                        <input type="hidden" name="title" value="<?= htmlspecialchars($purchase['title']) ?>">
                        <button type="submit" class="btn" <?= $isFullyPaid ? 'disabled' : '' ?>>
                            <?= $isFullyPaid ? 'Paid' : 'Pay' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>


    </table>
<?php endif;
include '../footer.php'; ?>

</body>

</html>