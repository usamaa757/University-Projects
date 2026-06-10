<?php
include '../db.php';
include 'header.php';

$agent_id = $_SESSION['user_id'] ?? null;



// Fetch terminated rentals for properties managed by this agent
$sql = "
    SELECT r.*, u.fullname, p.title 
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN properties p ON r.property_id = p.id
    WHERE r.status = 'terminated' AND p.agent_id = ?
    ORDER BY r.terminated_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container">
    <div class="section-header">

        <h2>Terminated Rentals</h2>
    </div>
    <?php if ($result->num_rows === 0): ?>
        <p>No terminated rentals found.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>User</th>
                <th>Property</th>
                <th>Monthly Rent</th>
                <th>Paid Months</th>
                <th>Terminated At</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td>$ <?= number_format($row['monthly_rent']) ?></td>
                    <td><?= $row['paid_months'] ?></td>
                    <td><?= $row['terminated_at'] ?? 'N/A' ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>