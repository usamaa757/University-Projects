<?php
include '../db.php';
include 'header.php';

$agentId = $_SESSION['user_id'] ?? null;

// ✅ Auto-terminate rentals that reached or exceeded the total months
$conn->query("
    UPDATE rentals 
    SET status = 'terminated', terminated_at = NOW()
    WHERE status = 'active' AND paid_months >= total_months
");

// Fetch active rentals
$sql = "
    SELECT 
        r.id AS rental_id,
        p.title,
        p.address,
        r.user_id,
        u.fullname AS tenant_name,
        r.monthly_rent,
        r.total_months,
        r.paid_months,
        r.next_due_date,
        r.created_at
    FROM rentals r
    JOIN properties p ON r.property_id = p.id
    JOIN users u ON r.user_id = u.id
    WHERE p.agent_id = ? AND r.status = 'active'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agentId);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="section-header">
    <h2>My Rented Properties</h2>
</div>

<?php if ($result->num_rows === 0): ?>
    <p>No rentals found.</p>
<?php else: ?>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Title</th>
                <th>Address</th>
                <th>Tenant Name</th>
                <th>Monthly Rent</th>
                <th>Paid Months</th>
                <th>Total Months</th>
                <th>Next Due Date</th>
                <th>Start Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rental = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($rental['title']) ?></td>
                    <td><?= htmlspecialchars($rental['address']) ?></td>
                    <td><?= htmlspecialchars($rental['tenant_name']) ?></td>
                    <td>$ <?= number_format($rental['monthly_rent']) ?></td>
                    <td><?= $rental['paid_months'] ?></td>
                    <td><?= $rental['total_months'] ?></td>
                    <td><?= $rental['next_due_date'] ?></td>
                    <td><?= $rental['created_at'] ?></td>
                    <td>
                        <a href="terminate_rent.php?rental_id=<?= $rental['rental_id'] ?>"
                            onclick="return confirm('Are you sure you want to terminate this rental?');" class="delete-btn"
                            style="padding: 5px; border-radius: 5px">
                            Terminate
                        </a>
                        <a href="update_rental.php?rental_id=<?= $rental['rental_id'] ?>" class="edit-btn"
                            style="padding: 5px; border-radius: 5px; margin-left: 5px;">Update Rent</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../footer.php'; ?>