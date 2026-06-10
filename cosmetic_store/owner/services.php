<?php
include 'header.php';
include '../db.php';

$query = "SELECT * FROM services"; // Replace with your actual table name
$result = mysqli_query($conn, $query);
?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Manage services & Services</h2>
        <a href="add_service.php" class="btn">+ Add
            New</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['service_name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td>
                            <a href="edit_service.php?service_id=<?= $row['service_id'] ?>" class="edit_button">Edit</a>
                            <a href="delete_service.php?service_id=<?= $row['service_id'] ?>" class="delete_button"
                                onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No services found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>

</html>