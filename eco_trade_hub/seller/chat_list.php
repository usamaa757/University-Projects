<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];

// Fetch distinct sellers that the buyer has chatted with
$sql = "SELECT DISTINCT b.buyer_id, b.buyer_name
        FROM messages m
        JOIN buyers b ON m.buyer_id = b.buyer_id
        WHERE m.seller_id = ?
        UNION
        SELECT DISTINCT b.buyer_id, b.buyer_name
        FROM messages m
        JOIN buyers b ON m.buyer_id = b.buyer_id
        WHERE m.seller_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $seller_id, $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h3 class="text-center">Chat List</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <a href="chat.php?buyer_id=<?php echo htmlspecialchars($row['buyer_id']); ?>">
                        <?php echo htmlspecialchars($row['buyer_name']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No data found</li>
        <?php endif; ?>
    </ul>
</div>
