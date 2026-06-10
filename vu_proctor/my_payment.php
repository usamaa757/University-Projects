<?php
include 'navbar.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch payment records
$payments = $conn->query("
    SELECT id, total_duties, leaves, total_present, amount, status, last_updated
    FROM payments
    WHERE user_id = $user_id
    ORDER BY last_updated DESC
");
?>

<style>
    .summary {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .card {
        flex: 1;
        min-width: 220px;
        background: #f9fafb;
        padding: 14px;
        border-radius: 10px;
        text-align: center;
        box-shadow: inset 0 0 0 1px #e5e7eb;
    }

    .card h4 {
        margin: 0;
        font-size: 14px;
        color: #6b7280;
    }

    .card p {
        margin: 6px 0 0;
        font-size: 18px;
        font-weight: bold;
        color: #111827;
    }


    .status {
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status.processed {
        background: #dcfce7;
        color: #166534;
    }

    @media (max-width: 640px) {
        .card p {
            font-size: 16px;
        }

        th,
        td {
            font-size: 13px;
        }
    }
</style>
</head>

<body>
    <div class="table-container">
        <h2>💰 My Payments</h2>
        <p style="color:#6b7280;font-size:14px;margin-bottom:20px;">
            Below are your payment details based on verified duties and approved leaves.
        </p>

        <!-- Quick summary -->
        <?php
        $summary = $conn->query("
      SELECT 
        SUM(total_duties) AS duties,
        SUM(leaves) AS leaves,
        SUM(total_present) AS present,
        SUM(amount) AS total_amount
      FROM payments
      WHERE user_id = $user_id
  ")->fetch_assoc();
        ?>
        <div class="summary">
            <div class="card">
                <h4>Total Duties</h4>
                <p><?= $summary['duties'] ?? 0 ?></p>
            </div>
            <div class="card">
                <h4>Leaves Taken</h4>
                <p><?= $summary['leaves'] ?? 0 ?></p>
            </div>
            <div class="card">
                <h4>Days Present</h4>
                <p><?= $summary['present'] ?? 0 ?></p>
            </div>
            <div class="card">
                <h4>Total Earned (₨)</h4>
                <p><?= number_format($summary['total_amount'] ?? 0) ?></p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Duties</th>
                        <th>Leaves</th>
                        <th>Present Days</th>
                        <th>Amount (₨)</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payments->num_rows > 0): ?>
                        <?php while ($p = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('F Y', strtotime($p['last_updated'])) ?></td>
                                <td><?= $p['total_duties'] ?></td>
                                <td><?= $p['leaves'] ?></td>
                                <td><?= $p['total_present'] ?></td>
                                <td><?= number_format($p['amount']) ?></td>
                                <td><span class="status <?= strtolower($p['status']) ?>"><?= ucfirst($p['status']) ?></span>
                                </td>
                                <td><?= date('Y-m-d', strtotime($p['last_updated'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:#6b7280;">No payment records yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>