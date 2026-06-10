
<?php 
include("header.php");
include("../db_connection.php");

// Check if the user is logged in and is a seller
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT reviews.*, buyers.buyer_name
        FROM reviews
        JOIN buyers ON reviews.buyer_id = buyers.buyer_id
        WHERE reviews.seller_id = ?
        ORDER BY reviews.review_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Feadback & Review</h3>
                <div class="p-3">
<?php while ($row = $result->fetch_assoc()) : ?>
    <div class="review">
        <h4><?php echo htmlspecialchars($row['buyer_name']); ?> (Rating: <?php echo htmlspecialchars($row['rating']); ?>/5)</h4>
        <p><?php echo htmlspecialchars($row['review_text']); ?></p>
        <small><?php echo htmlspecialchars($row['review_date']); ?></small>
    </div>
<?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

