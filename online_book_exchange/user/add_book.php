<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_title = $_POST['book_title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $condition = $_POST['condition'];
    $location = $_POST['location'];

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_temp = $_FILES['image']['tmp_name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $image_extension;
        move_uploaded_file($image_temp, 'images/' . $image); // Store image in 'images' folder
    }

    // Insert the book listing into the database
    $stmt = $conn->prepare("INSERT INTO books (user_id, book_title, author, genre, condition_state, location, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $book_title, $author, $genre, $condition, $location, $image);

    if ($stmt->execute()) {
        $success_message = "Book listing added successfully!";
    } else {
        $error_message = "Error adding book listing.";
    }

    $stmt->close();
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Add Book Listing</h3>
                <div class="p-4">
                    <?php if (isset($success_message)) : ?>
                        <div class="text-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_message)) : ?>
                        <div class="text-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="book_title">Book Title</label>
                            <input type="text" class="form-control" id="book_title" name="book_title" required>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <input type="text" class="form-control" id="genre" name="genre" required>
                        </div>
                        <div class="form-group">
                            <label for="condition">Condition</label>
                            <select class="form-control" id="condition" name="condition" required>
                                <option value="New">New</option>
                                <option value="Like New">Like New</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Poor">Poor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Book Image</label>
                            <input type="file" class="form-control-file" id="image" name="image">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add Book</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>