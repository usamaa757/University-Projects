<?php
include 'header.php';
include '../db_connection.php';


if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Fetch book details from the database
    $sql = "SELECT * FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_title = $_POST['book_title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $condition_state = $_POST['condition_state'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Update book details in the database
    $sql = "UPDATE books SET book_title = ?, author = ?, genre = ?, condition_state = ?, location = ?, status = ? WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $book_title, $author, $genre, $condition_state, $location, $status, $book_id);
    if ($stmt->execute()) {
        // Store success message in session
        $_SESSION['msg'] = "Book updated successfully!";
        header("Location: edit_book.php?book_id=" . $book_id);
        exit();
    } else {
        $msg = "Error updating book details.";
    }
}

// Display success message from session
if (isset($_SESSION['msg'])) {
    $successMsg = $_SESSION['msg'];
    unset($_SESSION['msg']); // Clear the message after displaying
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Edit Book</h3>
                <?php if (isset($msg)): ?>
                    <div class="text-danger p-2"><?php echo $msg; ?></div>
                <?php endif; ?>
                <?php if (isset($successMsg)): ?>
                    <div class="text-success p-2"><?php echo $successMsg; ?></div>
                <?php endif; ?>
                <form method="POST" class="p-3">
                    <div class="form-group">
                        <label for="book_title">Book Title</label>
                        <input type="text" id="book_title" name="book_title" class="form-control"
                            value="<?php echo htmlspecialchars($book['book_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" id="author" name="author" class="form-control"
                            value="<?php echo htmlspecialchars($book['author']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <input type="text" id="genre" name="genre" class="form-control"
                            value="<?php echo htmlspecialchars($book['genre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="condition_state">Condition</label>
                        <input type="text" id="condition_state" name="condition_state" class="form-control"
                            value="<?php echo htmlspecialchars($book['condition_state']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control"
                            value="<?php echo htmlspecialchars($book['location']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="available" <?php echo ($book['status'] == 'available') ? 'selected' : ''; ?>>
                                Available
                            </option>
                            <option value="exchanged" <?php echo ($book['status'] == 'exchanged') ? 'selected' : ''; ?>>
                                Exchanged
                            </option>

                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>