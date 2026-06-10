<?php
include 'header.php';
include '../db_connection.php';

// Check if user_book_id is passed in the URL
if (isset($_GET['user_book_id'])) {
    $user_book_id = $_GET['user_book_id'];

    // Query to fetch the book details based on the book_id
    $sql = "SELECT * FROM books 
            WHERE book_id = ?";

    // Prepare and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_book_id); // Bind the book_id parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the book exists
        if ($result->num_rows > 0) {
            $book_details = $result->fetch_assoc();
        } else {
            // Handle case if book is not found
            header("Location: books_list.php?error=Book not found.");
            exit();
        }
    } else {
        // Error in the query
        header("Location: books_list.php?error=Error fetching book details.");
        exit();
    }
} else {
    // If no book_id is passed
    header("Location: books_list.php?error=Invalid request.");
    exit();
}
?>

<div class="container my-5">
    <h2 class="text-center">Book Details</h2>

    <!-- Display Book Details -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><?php echo htmlspecialchars($book_details['book_title']); ?></h4>
        </div>
        <div class="card-body">
            <p><strong>Author:</strong> <?php echo htmlspecialchars($book_details['author']); ?></p>
            <p><strong>Condition:</strong> <?php echo htmlspecialchars($book_details['condition_state']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($book_details['genre']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($book_details['location']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($book_details['status']); ?></p>
            <p><strong>Created on:</strong> <?php echo date('d M, Y', strtotime($book_details['create_date'])); ?>
            </p>

            <!-- Image display if available -->
            <?php if (!empty($book_details['image'])): ?>
                <img src="images/<?php echo htmlspecialchars($book_details['image']); ?>" alt="Book Image"
                    class="img-fluid">
            <?php endif; ?>

            <!-- Button to go back to books list -->
            <a href="request_book_list.php" class="btn btn-primary mt-3">Back to Books List</a>
        </div>
    </div>
</div>
</body>

</html>