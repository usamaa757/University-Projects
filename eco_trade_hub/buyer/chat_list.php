<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// Fetch distinct sellers that the buyer has chatted with
$sql = "SELECT DISTINCT s.seller_id, s.seller_name
        FROM messages m
        JOIN sellers s ON m.seller_id = s.seller_id
        WHERE m.buyer_id = ?
        UNION
        SELECT DISTINCT s.seller_id, s.seller_name
        FROM messages m
        JOIN sellers s ON m.seller_id = s.seller_id
        WHERE m.seller_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $buyer_id, $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h3 class="text-center">Chat List</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <a href="chat.php?seller_id=<?php echo htmlspecialchars($row['seller_id']); ?>">
                        <?php echo htmlspecialchars($row['seller_name']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No data found</li>
        <?php endif; ?>
    </ul>
</div>
