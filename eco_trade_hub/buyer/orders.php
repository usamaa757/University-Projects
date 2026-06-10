<?php

include("../db_connection.php");
include("header.php");

// Assume the user is logged in and their user ID is stored in the session
$buyer_id = $_SESSION['buyer_id'];

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php?msg=" . urlencode("Please log in as buyer first."));
    exit();
   
}

// Fetch all orders for the logged-in user
$stmt = $conn->prepare("
    SELECT o.order_id, o.total, o.order_date, oi.part_id, oi.price, ap.part_name, ap.model, ap.make
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN auto_parts ap ON oi.part_id = ap.part_id
    WHERE o.buyer_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['order_date'] = $row['order_date'];
    $orders[$row['order_id']]['items'][] = [
        'part_id' => $row['part_id'],
        'part_name' => $row['part_name'],
        'model' => $row['model'],
        'make' => $row['make'],
        'price' => $row['price']
    ];
}
?>

    <div class="container mt-3">
        <h3 class="mb-4">My Orders</h3>
        
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order_id => $order): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?> <br>
                        <strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?> <br>
                      
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Part Name</th>
                                    <th>Model</th>
                                    <th>Make</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['part_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['model']); ?></td>
                                        <td><?php echo htmlspecialchars($item['make']); ?></td>
                                        <td>R. <?php echo htmlspecialchars($item['price']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No orders found.</div>
        <?php endif; ?>

        <a href="products_list.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</body>
</html>
