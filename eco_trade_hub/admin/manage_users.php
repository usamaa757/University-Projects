<?php

include("admin_header.php");
include("../db_connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location:  ../login.php?msg=" . urlencode("Please log in as admin first."));
    exit();
}

// Fetch pending buyers and sellers
$buyers_result = $conn->query("SELECT * FROM buyers");
$sellers_result = $conn->query("SELECT * FROM sellers");
?>

<div class="container mt-3">
    <a href="admin_dashboard.php"><button class="btn btn-primary mb-2">Back to Dashboard</button></a>
    <div class="border shadow-sm rounded">
        <h2 class="text-center bg-dark p-2 text-white">User Requests</h2>
        <div class="p-3">

            <h4>Buyers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($buyers_result->num_rows > 0) : ?>
                        <?php while ($row = $buyers_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['buyer_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['buyer_id']); ?>">
                                        <input type="hidden" name="user_type" value="buyers">
                                        <input type="hidden" name="action" value="approve">
                                        <a href="edit_buyer.php?buyer_id=<?php echo $row['buyer_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    </form>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <h4>Sellers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sellers_result->num_rows > 0) : ?>
                        <?php while ($row = $sellers_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['seller_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['seller_id']); ?>">
                                        <input type="hidden" name="user_type" value="sellers">
                                        <input type="hidden" name="action" value="approve">
                                        <a href="edit_seller.php?seller_id=<?php echo $row['seller_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

</html>