<?php
include 'header.php';
include '../other/db_connection.php';

// Fetch fee vouchers
$sql = "
SELECT fv.voucher_id, c.class_name, fv.fee_amount, fv.month, fv.year, fv.issue_date, fv.due_date 
FROM fee_vouchers fv
JOIN classes c ON fv.class_id = c.class_id
ORDER BY fv.issue_date DESC";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Fee Vouchers</title>
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <div class="container">
        <h3>Fee Vouchers</h3>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Voucher ID</th>
                        <th>Class Name</th>
                        <th>Fee Amount</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['voucher_id']); ?></td>
                            <td><?= htmlspecialchars($row['class_name']); ?></td>
                            <td><?= htmlspecialchars($row['fee_amount']); ?></td>
                            <td><?= htmlspecialchars($row['month']); ?></td>
                            <td><?= htmlspecialchars($row['year']); ?></td>
                            <td><?= htmlspecialchars($row['issue_date']); ?></td>
                            <td><?= htmlspecialchars($row['due_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No fee vouchers found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
