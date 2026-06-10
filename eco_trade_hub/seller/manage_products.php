<?php

include("header.php");
include("../db_connection.php");

// Check if the user is logged in and is a seller
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id = $_SESSION['seller_id'];
    $part_name = $conn->real_escape_string($_POST['part_name']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $price = $conn->real_escape_string($_POST['price']);
    $make = $conn->real_escape_string($_POST['make']);
    $model = $conn->real_escape_string($_POST['model']);
    $location = $conn->real_escape_string($_POST['location']);
    $image_path = '';

    // Define allowed file extensions
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_mime_type = mime_content_type($_FILES['image']['tmp_name']);

        // Check file extension and MIME type
        if (in_array($file_extension, $allowed_extensions) && strpos($file_mime_type, 'image/') === 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = basename($_FILES["image"]["name"]);
            } else {
                $msg = "Sorry, there was an error uploading your file.";
            }
        } else {
            $msg = "Invalid file type. Only images are allowed.";
        }
    }

    // Insert product details into the database
    if ($msg == '') {
        $sql = "INSERT INTO auto_parts (`seller_id`, `part_name`, `condition`, `price`, `location`, `make`, `model`, `images`) 
                VALUES ('$seller_id', '$part_name', '$condition', '$price', '$location', '$make', '$model', '$image_path')";
        if ($conn->query($sql) === TRUE) {
            $msg = "Product added successfully.";
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h2 class="text-center heading-bg bg-dark text-white p-2">List a New Product</h2>
                <div class="p-4">
                    <?php if ($msg != '') : ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($msg); ?>
                        </div>
                    <?php endif; ?>
                    <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="part_name">Part Name:</label>
                            <input type="text" class="form-control" id="part_name" name="part_name" required>
                        </div>
                        <div class="form-group">
                            <label for="condition">Condition:</label>
                            <input type="text" class="form-control" id="condition" name="condition" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="make">Make:</label>
                            <input type="text" class="form-control" id="make" name="make" required>
                        </div>
                        <div class="form-group">
                            <label for="model">Model:</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location:</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Image:</label>
                            <input type="file" class="form-control" accept="image/*" id="image" name="image">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>
