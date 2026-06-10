<?php
include '../db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT r.id AS rental_id, p.title, r.monthly_rent, r.total_months, r.paid_months, r.next_due_date, p.id
        FROM rentals r 
        JOIN properties p ON r.property_id = p.id
        WHERE r.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="section-header">

    <h2>Your Rent Payments</h2>
</div>
<table>
    <tr>
        <th>Property</th>
        <th>Monthly Rent</th>
        <th>Paid Months</th>
        <th>Next Due Date</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php $isComplete = $row['paid_months'] >= $row['total_months']; ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td>$ <?= number_format($row['monthly_rent']) ?></td>
            <td><?= $row['paid_months'] ?> / <?= $row['total_months'] ?></td>
            <td><?= $row['next_due_date'] ?></td>
            <td>
                <?php if ($isComplete): ?>
                    <button disabled class="btn">Completed</button>
                <?php else: ?>
                    <form method="POST" action="rent_payment_process.php">
                        <input type="hidden" name="rental_id" value="<?= $row['rental_id'] ?>">
                        <input type="hidden" name="amount" value="<?= $row['monthly_rent'] ?>">
                        <input type="hidden" name="property_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn">Pay</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../footer.php'; ?>