<?php
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Only for sellers
if ($_SESSION['role'] != 'seller') {
    die("<p>Access denied. Only sellers can view feedback.</p>");
}

$seller_id = $_SESSION['user_id'];

// Fetch feedback for this seller
$result = mysqli_query($conn, "SELECT f.*, u.name AS buyer_name, fur.category, fur.name AS furniture_name
                               FROM feedback f
                               JOIN orders o ON f.order_id = o.id
                               JOIN users u ON f.buyer_id = u.id
                               JOIN furniture fur ON o.furniture_id = fur.id
                               WHERE o.seller_id = '$seller_id'
                               ORDER BY f.created_at DESC");
?>

<div class="container">
    <h3>Feedback Received</h3>
    <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <tr>
            <th>Buyer</th>
            <th>Furniture</th>
            <th>Category</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
            <td><?php echo htmlspecialchars($row['furniture_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['rating']); ?>/5</td>
            <td><?php echo htmlspecialchars($row['comments']); ?></td>
            <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <p>No feedback received yet.</p>
    <?php endif; ?>
</div>