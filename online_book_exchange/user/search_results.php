<?php

include("../db_connection.php");
include("header.php");
$user = $_SESSION['user_id'];
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Prepare the SQL query to search for parts
$stmt = $conn->prepare("SELECT * FROM books WHERE book_title LIKE ? OR author LIKE ? OR `condition_state` LIKE ? OR `location` LIKE ? AND user_id != ?");
$searchTerm = '%' . $searchQuery . '%';
$stmt->bind_param("ssssi", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-3">
    <h4 class="mb-4">Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h4>

    <?php if ($result->num_rows > 0) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>State</th>
                    <th>Condition</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['condition_state']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td>
                            <form method="POST" action="exchange_request.php">
                                <input type="hidden" name="part_id" value="<?php echo $row['book_id']; ?>">
                                <button type="submit" class="btn btn-primary">Excahnge</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="text-info">
            No results found for "<?php echo htmlspecialchars($searchQuery); ?>".
        </div>
    <?php endif; ?>

    <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
</div>

</body>

</html>