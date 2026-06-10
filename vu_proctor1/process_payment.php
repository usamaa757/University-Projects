<?php
include 'navbar.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

// ✅ Handle payment processing
if (isset($_GET['process'])) {
    $payment_id = intval($_GET['process']);
    $conn->query("UPDATE payments SET status = 'processed' WHERE id = $payment_id");
    $msg = "Payment marked as processed!";
}

// ✅ Fetch all payment records
$sql = "
    SELECT p.*, u.full_name, u.role
    FROM payments p
    JOIN users u ON p.user_id = u.id
    ORDER BY u.full_name ASC
";
$payments = $conn->query($sql);
?>

<div class="table-container">
    <h2>Payment Management</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>

    <table>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Total Duties</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $payments->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= ucfirst($row['role']) ?></td>
                <td><?= $row['total_duties'] ?></td>
                <td>PKR <?= number_format($row['amount'], 2) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] === 'pending'): ?>
                        <a href="?process=<?= $row['id'] ?>" onclick="return confirm('Mark payment as processed?');">Process</a>
                    <?php else: ?>
                        <em>Processed</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>