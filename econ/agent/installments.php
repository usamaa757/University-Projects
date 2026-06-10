<?php
include '../db.php';
include 'header.php';

$agentId = $_SESSION['user_id'];

// Fetch purchases for properties belonging to the logged-in agent
$sql = "SELECT 
            p.id AS purchase_id, 
            p.monthly_installment, 
            p.total_payment, 
            p.remaining_balance, 
            p.paid_installments, 
            p.period_years,
            pr.title,
            u.fullname AS buyer_name
        FROM purchases p
        JOIN properties pr ON p.property_id = pr.id
        JOIN users u ON p.user_id = u.id
        WHERE pr.agent_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agentId);
$stmt->execute();
$result = $stmt->get_result();

$purchases = [];
while ($row = $result->fetch_assoc()) {
    $purchases[] = $row;
}
$stmt->close();
?>

<div class="section-header">

    <h2>Installment Status of Your Properties</h2>
</div>

<?php if (count($purchases) === 0): ?>
    <p style="text-align:center;">No installment records for your properties yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Property</th>
            <th>Buyer</th>
            <th>Period (Years)</th>
            <th>Total/Paid Installments</th>
            <th>Monthly Installment</th>
            <th>Total Payment</th>
            <th>Remaining Balance</th>
        </tr>
        <?php foreach ($purchases as $purchase): ?>
            <?php
            $totalInstallments = $purchase['period_years'] * 12;
            $paidInstallments = $purchase['paid_installments'];
            $progressPercent = min(100, round(($paidInstallments / $totalInstallments) * 100));
            ?>
            <tr>
                <td><?= htmlspecialchars($purchase['title']) ?></td>
                <td><?= htmlspecialchars($purchase['buyer_name']) ?></td>
                <td><?= $purchase['period_years'] ?></td>
                <td>
                    <?= $paidInstallments ?> / <?= $totalInstallments ?>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?= $progressPercent ?>%;"></div>
                    </div>
                </td>
                <td>$ <?= number_format($purchase['monthly_installment']) ?></td>
                <td>$ <?= number_format($purchase['total_payment']) ?></td>
                <td>$ <?= number_format($purchase['remaining_balance']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif;
include '../footer.php'; ?>