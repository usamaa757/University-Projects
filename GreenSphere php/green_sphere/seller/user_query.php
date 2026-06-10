<?php
include 'header.php';
include '../db_connection.php';

// Get the logged-in seller's user_id from the session
$seller_id = $_SESSION['seller_id']; // Assuming the session contains the logged-in seller's user_id

// Fetch all plant queries for plants owned by the seller
$queries_query = "
    SELECT pq.query_id, pq.user_id, pq.query_title, pq.query_description, pq.status, pq.response, p.plant_name, u.user_name
    FROM plant_queries pq
    JOIN plants p ON pq.plant_id = p.plant_id
    JOIN users u ON pq.user_id = u.user_id
    WHERE p.seller_id = ?"; // Filter queries based on the seller's plant ownership

$stmt = $conn->prepare($queries_query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$queries_result = $stmt->get_result();

// Handle marking a query as answered or responding
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respond_query'])) {
    $query_id = $_POST['query_id'];
    $response = $_POST['response'];
    $status = 'Answered'; // Set the status to "Answered" after the seller responds

    // Update the query with the seller's response
    $update_query = "UPDATE plant_queries SET response = ?, status = ? WHERE query_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $response, $status, $query_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Query has been marked as answered.";
    } else {
        $_SESSION['error'] = "There was an error responding to the query.";
    }

    header("Location: user_query.php");
    exit();
}
?>

<div class="container mt-4 round shadow border">
    <div class="text-center p-3">
        <h3>Your Plant Queries</h3>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php elseif (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Display Queries -->
    <table class="table table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th>#</th>
                <th>Plant</th>
                <th>User</th>
                <th>Query Title</th>
                <th>Status</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($query = $queries_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $query['query_id']; ?></td>
                <td><?php echo htmlspecialchars($query['plant_name']); ?></td>
                <td><?php echo htmlspecialchars($query['user_name']); ?></td>
                <td><?php echo htmlspecialchars($query['query_title']); ?></td>
                <td><?php echo htmlspecialchars($query['status']); ?></td>
                <td>
                    <?php if ($query['status'] == 'Answered'): ?>
                    <p><?php echo nl2br(htmlspecialchars($query['response'])); ?></p>
                    <?php else: ?>
                    <form action="user_query.php" method="POST">
                        <input type="hidden" name="query_id" value="<?php echo $query['query_id']; ?>">
                        <div class="form-group">
                            <textarea class="form-control" name="response" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="respond_query" class="btn btn-primary">Respond</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>

</html>