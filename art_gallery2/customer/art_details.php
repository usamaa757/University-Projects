<?php
include '../db.php';
include 'header.php';
// Check if ID is set in the URL
if (!isset($_GET['art_id']) || !is_numeric($_GET['art_id'])) {
    die("Invalid artwork ID.");
}

$art_id = $_GET['art_id'];

// Fetch Artwork Details
$sql = "SELECT a.*, u.username
        FROM art_items a
        JOIN users u ON a.seller_id = u.user_id
        WHERE a.art_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $art_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if artwork exists
if ($result->num_rows == 0) {
    die("Artwork not found.");
}

$artwork = $result->fetch_assoc();
?>

<!-- Artwork Details -->
<div class="container mt-5 border rounded shadow">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo $base_url . 'seller/' . htmlspecialchars($artwork['image']); ?>" class="img-fluid p-3 "
                alt="Artwork" width="400" height="400">

        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($artwork['art_name']); ?></h2>
            <p class="text-muted">By <?php echo htmlspecialchars($artwork['username']); ?></p>
            <h4 class="text-success">$<?php echo number_format($artwork['price'], 2); ?></h4>
            <p><?php echo nl2br(htmlspecialchars($artwork['description'])); ?></p>
            <a href="add_to_cart.php?art_id=<?= $artwork['art_id']; ?>" class="btn btn-warning">Add to Cart</a>

            <a href="art_list.php" class="btn btn-secondary">Back to Gallery</a>
            <a href="reviews.php?art_id=<?= $artwork['art_id']; ?>" class="btn btn-primary">Reviews</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Arts Gallery. All Rights Reserved.</p>
</footer>

</body>

</html>

<?php
// Close Database Connection
$conn->close();
?>