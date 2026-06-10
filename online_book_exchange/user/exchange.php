<?php
include 'header.php';
include '../db_connection.php';

if (isset($_GET['book_id']) && isset($_GET['user_id'])) {
    $book_id = $_GET['book_id']; // Requested book ID
    $requested_to = $_GET['user_id']; // Owner of the requested book
    $requested_by = $_SESSION['user_id']; // Current user ID

    // Fetch details of the requested book
    $stmt = $conn->prepare("SELECT b.book_title, u.user_name 
                            FROM books b 
                            JOIN users u ON b.user_id = u.user_id 
                            WHERE b.book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result_requested_book = $stmt->get_result()->fetch_assoc();

    // Fetch books of the current user
    $stmt = $conn->prepare("SELECT book_id, book_title 
                            FROM books 
                            WHERE user_id = ?");
    $stmt->bind_param("i", $requested_by);
    $stmt->execute();
    $result_user_books = $stmt->get_result();
} else {
    header("Location: books_list.php?error=Invalid request.");
    exit();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Exchange Details</h3>
                <?php
                if (isset($_GET['msg'])) {
                    // Display success message
                    echo '<div class="text-success" role="alert">' . htmlspecialchars($_GET['msg']) . '</div>';
                }

                if (isset($_GET['error'])) {
                    // Display error message
                    echo '<div class="text-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                ?>

                <div class="card mb-4">
                    <div class="text-center">
                        <h4 class="mb-0">Requested Book</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Title:</strong> <?php echo htmlspecialchars($result_requested_book['book_title']); ?>
                        </p>
                        <p><strong>Owner:</strong> <?php echo htmlspecialchars($result_requested_book['user_name']); ?>
                        </p>
                    </div>
                </div>

                <!-- User Books and Exchange Form -->
                <div class="card">
                    <div class="text-center">
                        <h4 class="mb-0">Your Books</h4>
                    </div>
                    <div class="card-body">
                        <form action="exchange_process.php" method="POST">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <input type="hidden" name="requested_to" value="<?php echo $requested_to; ?>">
                            <input type="hidden" name="requested_by" value="<?php echo $requested_by; ?>">

                            <div class="mb-3">
                                <label for="user_book" class="form-label">Select Your Book to Exchange:</label>
                                <select name="user_book" id="user_book" class="form-select" required>
                                    <?php while ($user_book = $result_user_books->fetch_assoc()): ?>
                                    <option value="<?php echo $user_book['book_id']; ?>">
                                        <?php echo htmlspecialchars($user_book['book_title']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="text-center">

                                <button type="submit" class="btn btn-primary">Confirm Exchange</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>

</html>