<?php
include("header.php");
include("../db_connection.php");

// Check if the user is logged in and is a buyer
if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php?msg=" . urlencode("Please log in as buyer first."));
    exit();
}

$msg = '';
if (isset($_GET['msg'])) {
    $msg = urldecode($_GET['msg']);
}

// Fetch auto parts from the database
$sql = "SELECT auto_parts.*, sellers.seller_name 
FROM auto_parts 
JOIN sellers ON auto_parts.seller_id = sellers.seller_id 
WHERE auto_parts.status = 'show'";
$result = $conn->query($sql);
?>

<!-- HTML to Display Products -->
<div class="container mt-4">
    <div class="row">
        <?php if ($msg != '') : ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars(BASE_PATH . '/seller/uploads/' . $row['images']); ?>" class="card-img-top img-fluid" alt="Product Image" style="max-height: 250px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['part_name']); ?></h5>
                            <p class="card-text">Condition: <?php echo htmlspecialchars($row['condition']); ?></p>
                            <p class="card-text">Price: Rs <?php echo htmlspecialchars($row['price']); ?></p>
                            <p class="card-text">Make: <?php echo htmlspecialchars($row['make']); ?></p>
                            <p class="card-text">Model: <?php echo htmlspecialchars($row['model']); ?></p>
                            <p class="card-text">Location: <?php echo htmlspecialchars($row['location']); ?></p>

                            <a href="place_order.php?part_id=<?php echo $row['part_id']; ?>" class="btn btn-sm mt-2 btn-primary">Purchase</a>

                            <a href="chat.php?seller_id=<?php echo htmlspecialchars($row['seller_id']); ?>" class="btn btn-success btn-sm mt-2">Chat with <?php echo htmlspecialchars($row['seller_name']); ?></a>
                            <a href="review.php?seller_id=<?php echo htmlspecialchars($row['seller_id']); ?>" class="btn btn-warning btn-sm mt-2">Review</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</div>