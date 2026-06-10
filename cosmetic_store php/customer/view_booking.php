<?php
include 'header.php';
include '../db.php';
$customer_id = $_SESSION['customer_id'];
// Fetch all bookings
$query = "SELECT b.*, s.service_name, c.name AS customer_name 
FROM bookings b 
JOIN services s ON b.service_id = s.service_id
JOIN customer c ON b.customer_id = c.customer_id
WHERE b.customer_id = $customer_id";
$result = mysqli_query($conn, $query);
?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Manage Bookings</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Service</th>
                <th>Booking Date</th>
                <th>Booking Time</th>
                <th>Special Requests</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['service_name']) ?></td>
                <td><?= htmlspecialchars($row['booking_date']) ?></td>
                <td><?= htmlspecialchars($row['booking_time']) ?></td>
                <td><?= htmlspecialchars($row['special_requests']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>

</html>