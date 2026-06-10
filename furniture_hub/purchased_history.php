<?php
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch purchased history for buyers
if ($_SESSION['role'] == 'buyer') {
    $query = "SELECT o.id AS order_id, f.*, u.name AS seller_name, u.email AS seller_email
              FROM orders o
              JOIN furniture f ON o.furniture_id = f.id
              JOIN users u ON o.seller_id = u.id
              WHERE o.buyer_id='$user_id'
              ORDER BY o.id DESC";
} else { // seller: see all sold furniture
    $query = "SELECT o.id AS order_id, f.*, u.name AS buyer_name, u.email AS buyer_email
              FROM orders o
              JOIN furniture f ON o.furniture_id = f.id
              JOIN users u ON o.buyer_id = u.id
              WHERE f.seller_id='$user_id'
              ORDER BY o.id DESC";
}

$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h3>Purchased History</h3>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Description</th>
            <th>Condition</th>
            <th>Location</th>
            <th>Status</th>
            <th>Seller</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" width="80" height="80"
                    style="border-radius:5px; object-fit:cover;"></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td>Pkr <?php echo htmlspecialchars($row['price']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['condition_status']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>

            <td><?php echo htmlspecialchars($row['seller_name']); ?>
                (<?php echo htmlspecialchars($row['seller_email']); ?>)</td>
            <td class="action-links">
                <a href="give_feedback.php?order_id=<?php echo $row['order_id']; ?>">Feedback</a>
                <a href="submit_complaint.php?order_id=<?php echo $row['order_id']; ?>">Complaint</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>