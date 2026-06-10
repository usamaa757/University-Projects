<?php
include 'header.php';
include '../db_connection.php';

// Fetch the user ID from the session
$requested_by = $_SESSION['user_id'];
$msg = isset($_GET['msg']) ? $_GET['msg'] : ''; // Get any message from URL query

// Query to fetch all exchange requests where the user is the requester, along with review details
$sql = "SELECT er.request_id, er.status, b.book_title AS requested_book, 
               u.user_name AS requested_to_user, er.request_date, er.requested_to, er.book_id,
               er_rev.rating, er_rev.review_date, er_rev.request_id AS review_request_id
        FROM exchange_requests er
        JOIN books b ON er.book_id = b.book_id
        JOIN users u ON er.requested_to = u.user_id
        LEFT JOIN exchange_reviews er_rev ON er.request_id = er_rev.request_id 
        WHERE er.requested_by = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requested_by);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container mt-5 border rounded shadow p-0" style="max-width: 1000px;">
    <h3 class="text-center bg-dark text-white p-2">Your Exchange Requests</h3>

    <?php if ($result->num_rows > 0): ?>
    <div class="row p-4">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-3">
            <div class="p-4 border rounded shadow-sm">
                <p><strong>Requested Book:</strong> <?php echo htmlspecialchars($row['requested_book']); ?></p>
                <p><strong>Requested To:</strong> <?php echo htmlspecialchars($row['requested_to_user']); ?></p>
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
                <?php endif; ?>

                <?php if ($row['status'] == 'accepted' && !$row['rating']): ?>
                <form action="submit_review.php" method="post">
                    <div class="form-group">
                        <label for="rating">Rating (1 to 5):</label>
                        <input type="number" id="rating" name="rating" class="form-control" min="1" max="5" required>

                        <!-- Hidden inputs for request details -->
                        <input type="hidden" name="requested_to"
                            value="<?php echo htmlspecialchars($row['requested_to']); ?>">
                        <input type="hidden" name="request_id"
                            value="<?php echo htmlspecialchars($row['request_id']); ?>">
                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($row['book_id']); ?>">

                        <!-- Textarea for the review -->
                        <label for="review_text">Your Review:</label>
                        <textarea id="review_text" name="review_text" class="form-control" rows="4" required></textarea>

                        <!-- Submit button -->
                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </div>
                </form>

                <?php endif; ?>

            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-info">No exchange requests found.</div>
    <?php endif; ?>
</div>

</body>

</html>