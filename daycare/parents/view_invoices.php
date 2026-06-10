<?php
// view_invoices.php
require_once '../db_connection.php'; // Include DB connection file

// Fetch all invoices from the database
$sql = "SELECT i.invoice_id, c.name AS child_name, i.total_amount, i.invoice_date, i.status
        FROM invoices i
        JOIN children c ON i.child_id = c.child_id
        ORDER BY i.invoice_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoices</title>
</head>

<body>

    <h2>Invoices</h2>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Child Name</th>
                <th>Invoice Date</th>
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['invoice_id']) ?></td>
                <td><?= htmlspecialchars($row['child_name']) ?></td>
                <td><?= htmlspecialchars($row['invoice_date']) ?></td>
                <td>$<?= number_format($row['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="4">No invoices found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>