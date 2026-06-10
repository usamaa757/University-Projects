<?php
include 'header.php';
include '../db_connection.php';

// Get the current user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch all books except those added by the current user
$sql = "SELECT * FROM books WHERE user_id != ? AND status= 'available' ORDER BY create_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container mt-5">
    <h3 class="text-center heading-bg bg-dark text-white p-2">Available Books</h3>
    <?php if (isset($_GET['msg'])): ?>
        <div class="text-success">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="text-danger">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row">

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Book Image -->
                        <?php if ($row['image']): ?>
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Book Image">
                        <?php else: ?>
                            <img src="images/default.jpg" class="card-img-top" alt="Default Image">
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['book_title']); ?></h5>
                            <p class="card-text"><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                            <p class="card-text"><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></p>
                            <p class="card-text"><strong>Condition:</strong>
                                <?php echo htmlspecialchars($row['condition_state']); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?>
                            </p>
                            <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                        </div>

                        <div class="card-footer text-center">
                            <!-- Chat Button -->
                            <a href="chat.php?book_id=<?php echo $row['book_id']; ?>&user_id=<?php echo $row['user_id']; ?>"
                                class="btn btn-sm btn-warning">
                                Chat
                            </a>

                            <a href="exchange.php?book_id=<?php echo $row['book_id']; ?>&user_id=<?php echo $row['user_id']; ?>"
                                class="btn btn-sm btn-warning">
                                Exchange
                            </a>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-danger">No books available.</p>
            </div>
        <?php endif; ?>
    </div>
</div>