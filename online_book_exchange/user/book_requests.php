<?php
include("../db_connection.php");
include("header.php");

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_title = $_POST['book_title'];
    $book_description = $_POST['book_description'];
    $author = $_POST['author'];

    $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_title, book_description, author) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $book_title, $book_description, $author);

    if ($stmt->execute()) {
        $success_message = "Request submitted successfully!";
    } else {
        $error_message = "Error submitting request.";
    }

    $stmt->close();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Book Request</h3>
                <div class="p-4">
                    <?php if (isset($success_message)) : ?>
                    <div class="text-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_message)) : ?>
                    <div class="text-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="book_title">Book Title</label>
                            <input type="text" class="form-control" id="book_title" name="book_title" required>
                        </div>
                        <div class="form-group">
                            <label for="book_description">Book Description</label>
                            <textarea class="form-control" id="book_description" name="book_description"
                                rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>