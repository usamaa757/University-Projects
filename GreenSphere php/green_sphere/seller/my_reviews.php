<?php
include 'header.php';
$seller_id = $_SESSION['seller_id'];
include '../db_connection.php';

// Fetch reviews for the seller
$sql = "SELECT r.rating, r.review_text, r.created_at, u.user_name 
        FROM seller_reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.seller_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<div class="container mt-5 shadow border round">
    <h3>Seller Reviews</h3>

    <?php if ($result->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item">
                    <h5><?php echo htmlspecialchars($row['user_name']); ?>
                        <span class="badge bg-success"><?php echo htmlspecialchars($row['rating']); ?> / 5</span>
                    </h5>
                    <p><?php echo htmlspecialchars($row['review_text']); ?></p>
                    <small class="text-muted"><?php echo htmlspecialchars($row['created_at']); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No reviews available for this seller.</div>
    <?php endif; ?>
</div>