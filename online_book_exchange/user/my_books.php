<?php
include 'header.php';
include '../db_connection.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM books WHERE user_id = ? ORDER BY create_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container mt-3">
    <h3 class="text-center heading-bg bg-dark text-white p-2">Your Book Listings</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Condition</th>
                <th>Location</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php $sno = 1;
                while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $sno++; ?></td>
                <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td><?php echo htmlspecialchars($row['genre']); ?></td>
                <td><?php echo htmlspecialchars($row['condition_state']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td>
                    <?php if ($row['image']): ?>
                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="Book Image" width="100">
                    <?php else: ?>
                    No image available
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="edit_book.php?book_id=<?php echo $row['book_id']; ?>" class="btn btn-sm btn-info">Edit</a>
                    <?php

                            if (htmlspecialchars($row['status']) == 'available') {
                            ?>
                    <a href="mark_exchange.php?book_id=<?php echo $row['book_id']; ?>&exchange=1"
                        class="btn btn-sm btn-warning">
                        Mark as Exchanged
                    </a>

                    <?php } else { ?>

                    <a href="mark_exchange.php?book_id=<?php echo $row['book_id']; ?>&available=1"
                        class="btn btn-sm btn-warning">
                        Mark as Available
                    </a>

                    <?php } ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" class="text-center text-danger">No books listed for exchange.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>