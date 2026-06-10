<?php
include("../db_connection.php");
include("header.php");

$user_id = $_SESSION['user_id'];

// Fetch book requests associated with this buyer

$sql = "SELECT 
            b.book_title AS requested_book, 
            er.status, 
            b.book_id, 
            er.request_id, 
            er.request_date,
            er.user_book_id, er.exchange_status,
            u.user_name AS requested_by_user, 
            ub.book_title AS user_book
        FROM exchange_requests er
        JOIN books b ON er.book_id = b.book_id
        JOIN users u ON er.requested_by = u.user_id
        JOIN books ub ON er.user_book_id = ub.book_id
        WHERE er.requested_by != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();


?>
<div class="container-fluid mt-3">
    <div class="border shadow bg-white rounded fluid">
        <h3 class="text-center heading-bg bg-dark text-white p-2">Book Requests</h3>
        <table class="table table-bordered">
            <?php if (isset($_GET['msg']) || isset($_GET['error'])): ?>
            <?php if (isset($_GET['msg'])): ?>
            <div class="text-success">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
            <?php elseif (isset($_GET['error'])): ?>
            <div class=" text-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Requested Book</th>
                    <th>User's Book</th>
                    <th>Requested By</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Exchange Status</th> <!-- Added column for exchange status -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['requested_book']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_book']); ?></td>
                    <td><?php echo htmlspecialchars($row['requested_by_user']); ?></td>
                    <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <!-- Display exchange status -->
                        <?php
                                if ($row['exchange_status'] == 'pending') {
                                    echo 'Pending';
                                } elseif ($row['exchange_status'] == 'pickup') {
                                    echo 'Pickup';
                                } elseif ($row['exchange_status'] == 'delivered') {
                                    echo 'Delivered';
                                } else {
                                    echo 'Unknown';
                                }
                                ?>
                    </td>
                    <td>
                        <a href="book_details.php?user_book_id=<?php echo $row['user_book_id']; ?>"
                            class="btn btn-sm btn-warning">Book Details</a>

                        <?php if ($row['status'] === 'pending' && $row['exchange_status'] == 'pending'): ?>
                        <a href="complete_exchange_request.php?request_id=<?php echo $row['request_id']; ?>&accept=1"
                            class="btn btn-sm btn-success">Accept</a>
                        <a href="complete_exchange_request.php?request_id=<?php echo $row['request_id']; ?>&decline=1"
                            class="btn btn-sm btn-danger">Decline</a>
                        <?php elseif ($row['exchange_status'] == 'pickup'): ?>
                        <a href="complete_exchange_request.php?request_id=<?php echo $row['request_id']; ?>&deliver=1"
                            class="btn btn-sm btn-primary">Mark as Delivered</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-danger">No book requests found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


</body>

</html>