<?php
include 'header.php';
include '../db_connection.php';

// Ensure that the user is logged in
$user_id = $_SESSION['user_id']; // Assuming user is logged in

// Fetch the user's queries along with plant and seller's responses
$query = "
    SELECT pq.query_id, pq.query_title, pq.query_description, pq.status, pq.response, p.plant_name, u.user_name 
    FROM plant_queries pq
    JOIN plants p ON pq.plant_id = p.plant_id
    LEFT JOIN users u ON pq.user_id = u.user_id
    WHERE pq.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$queries_result = $stmt->get_result();

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
    <a href="add_query.php" class="btn text-white bg-primary">Add New Query</a>

    <!-- Display Queries -->
    <table class="mt-2 table table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th>#</th>
                <th>Plant</th>
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
                    <td><?php echo htmlspecialchars($query['query_title']); ?></td>
                    <td><?php echo htmlspecialchars($query['status']); ?></td>
                    <td>
                        <?php if ($query['status'] == 'Answered'): ?>
                            <p><?php echo nl2br(htmlspecialchars($query['response'])); ?></p>
                        <?php else: ?>
                            <p>Your query is still being processed.</p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>

</html>