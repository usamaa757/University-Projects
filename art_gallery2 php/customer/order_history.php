<?php
include 'header.php';
include '../db.php';


$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, a.art_name, a.image, a.price 
        FROM orders o
        INNER JOIN art_items a ON o.art_id = a.art_id
        WHERE o.customer_id = $user_id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);
?>


<div class="container mt-5 border rounded shadow">


    <h3 class="text-center">My Orders</h3>

    <table class="table table-bordered">
        <tr>
            <thead class="table-dark">
                <th>Order ID</th>
                <th>Artwork</th>
                <th>Price</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Order Date</th>
                <th>Add Reviews</th>
            </thead>
            <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><img src="<?php echo $base_url . '/seller/' . $row['image']; ?>" width="50">
                <?php echo $row['art_name']; ?></td>
            <td>$<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['payment_method']; ?></td>
            <td><?php echo date("d M Y, H:i", strtotime($row['order_date'])); ?></td>
            <td>
                <a href="add_review.php?art_id=<?php echo $row['art_id']; ?>" class="btn btn-primary btn-sm">Add
                    Reviews</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>

</html>