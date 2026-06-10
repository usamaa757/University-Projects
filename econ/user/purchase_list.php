<?php
include '../db.php';
include 'header.php';
$user_id = $_SESSION['user_id'];
$sql = "SELECT p.*, pr.title, pr.city 
        FROM purchases p 
        JOIN properties pr ON p.property_id = pr.id
        WHERE user_id = $user_id
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);
?>
<div class="section-header">

    <h2>Confirmed Property Purchases</h2>
</div>

<?php if ($result && $result->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Property</th>
            <th>City</th>
            <th>Remaining Balance</th>
            <th>Tenure (Years)</th>
            <th>Interest (%)</th>
            <th>Monthly Installment</th>
            <th>Total Payment</th>
            <th>Confirmed On</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td>$ <?= number_format($row['remaining_balance']) ?></td>
            <td><?= $row['period_years'] ?> Years</td>
            <td><?= $row['markup_rate'] ?>%</td>
            <td>$ <?= number_format($row['monthly_installment']) ?></td>
            <td>$ <?= number_format($row['total_payment']) ?></td>
            <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
        </tr>

        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p style="text-align:center; color: gray; font-weight: bold;">No purchases found.</p>
<?php endif; ?>

<?php include '../footer.php'; ?>

</body>

</html>