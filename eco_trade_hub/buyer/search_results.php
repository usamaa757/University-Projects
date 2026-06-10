<?php

include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php?msg=" . urlencode("Please log in as buyer first."));
    exit();
   
}
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Prepare the SQL query to search for parts
$stmt = $conn->prepare("SELECT * FROM auto_parts WHERE part_name LIKE ? OR model LIKE ? OR make LIKE ? OR `condition` LIKE ?");
$searchTerm = '%' . $searchQuery . '%';
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-3">
    <h4 class="mb-4">Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h4>

    <?php if ($result->num_rows > 0) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Part Name</th>
                    <th>Model</th>
                    <th>Make</th>
                    <th>Condition</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['part_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                        <td><?php echo htmlspecialchars($row['make']); ?></td>
                        <td><?php echo htmlspecialchars($row['condition']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                        <form method="POST" action="purchase_parts.php">
    <input type="hidden" name="part_id" value="<?php echo $row['part_id']; ?>">
    <button type="submit" class="btn btn-primary">Purchase</button>
</form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-info">
            No results found for "<?php echo htmlspecialchars($searchQuery); ?>".
        </div>
    <?php endif; ?>

    <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
</div>

</body>

</html>