<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];

// Fetch orders associated with this seller's auto_parts
$sql = "SELECT orders.*, buyers.buyer_name, auto_parts.part_name
        FROM orders 
        JOIN buyers ON orders.buyer_id = buyers.buyer_id
        JOIN order_items ON orders.order_id = order_items.order_id
        JOIN auto_parts ON order_items.part_id = auto_parts.part_id
        WHERE auto_parts.seller_id = ?
        GROUP BY orders.order_id
        ORDER BY orders.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-fluid mt-3">
    <div class="border shadow bg-white rounded fluid">
        <h3 class="text-center heading-bg bg-dark text-white p-2">Orders</h3>
        <div class="p-2">
        <table class="table table-bordered">
            <?php if (isset($_GET['msg']) || isset($_GET['error'])): ?>
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Part Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['total']); ?></td>
                        <td><?php echo htmlspecialchars($row['part_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <a href="ship_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-sm btn-success">Ship</a>
                            <?php elseif($row['status'] == 'shipped'): ?>
                                <a href="confirm_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-sm btn-success">Confirm</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>

</html>
