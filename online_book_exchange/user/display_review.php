<?php
include 'header.php';
include '../db_connection.php';

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Prepare SQL query to fetch reviews for the logged-in user where the reviewer is not the logged-in user
$sql = "
    SELECT 
        er.review_id, 
        er.rating, 
        er.review_date, 
        er.requested_by_id, 
        er.requested_to_id, 
        er.book_id, 
        u.user_name AS reviewer_name, 
        b.book_title, 
        erq.request_date, 
        erq.status, 
        er.review_text
    FROM 
        exchange_reviews er
    JOIN
        users u ON u.user_id = er.requested_by_id
    JOIN
        books b ON b.book_id = er.book_id
    JOIN 
        exchange_requests erq ON er.request_id = erq.request_id
    WHERE 
        erq.status = 'accepted' 
        AND er.requested_to_id = ?  -- The logged-in user is the recipient
        AND er.requested_by_id != ?  -- Exclude reviews by the logged-in user
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id); // Bind the logged-in user ID for both requester and receiver
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5 border rounded shadow p-0" style="max-width: 1000px;">
    <h3 class="text-center bg-dark text-white p-2">Reviews by Other Users</h3>

    <?php if ($result->num_rows > 0): ?>
        <div class="row p-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="p-4 border rounded shadow-sm">
                        <p><strong>Book Title:</strong> <?php echo htmlspecialchars($row['book_title']); ?></p>
                        <p><strong>Reviewed By:</strong> <?php echo htmlspecialchars($row['reviewer_name']); ?></p>
                        <p><strong>Requested Date:</strong> <?php echo htmlspecialchars($row['request_date']); ?></p>
                        <p><strong>Status:</strong>
                            <span class="badge 
                                <?php
                                if ($row['status'] == 'pending') {
                                    echo 'badge-warning';
                                } elseif ($row['status'] == 'accepted') {
                                    echo 'badge-success';
                                } else {
                                    echo 'badge-danger';
                                }
                                ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </p>

                        <?php if ($row['rating']): ?>
                            <p><strong>Rating:</strong> <?php echo htmlspecialchars($row['rating']); ?>/5</p>
                            <p><strong>Review Date:</strong> <?php echo htmlspecialchars($row['review_date']); ?></p>
                            <p><strong>Review:</strong> <?php echo htmlspecialchars($row['review_text']); ?></p>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-danger p-3">No reviews from other users found.</div>
    <?php endif; ?>
</div>

</body>

</html>