<?php
include 'header.php';
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);

    $admin_id = $_SESSION['admin_id'];  // Assuming this is stored in the session

    // Handle image upload
    if (isset($_FILES['cloth_image']) && $_FILES['cloth_image']['error'] == 0) {
        $target_dir = "uploads/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($_FILES['cloth_image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type (only allow jpg, jpeg, png, gif)
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error_msg'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: add_cloths.php");
            exit();
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['cloth_image']['tmp_name'], $target_file)) {
            // File uploaded successfully, continue
            $image_path = $target_file;
        } else {
            $_SESSION['error_msg'] = "There was an error uploading the image.";
            header("Location: add_cloths.php");
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Please upload a valid image.";
        header("Location: add_cloths.php");
        exit();
    }

    // Insert data into the database
    // Prepare the SQL statement
    $query = "INSERT INTO cloths (category_id, description, price, quantity, size, image_url) 
VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, 'isdiss', $category_id, $description, $price, $quantity, $size, $image_path);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_msg'] = "Cloth item added successfully!";
        } else {
            $_SESSION['error_msg'] = "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_msg'] = "Error: " . mysqli_error($conn);
    }
}

// Fetch categories from the database
$category_query = "SELECT category_id, category_name FROM categories";
$category_result = mysqli_query($conn, $category_query);

// Close the connection
mysqli_close($conn);
?>

<div class="container mt-5 round border shadow p-3" style="max-width: 600px;">
    <h3>Add New Cloth Item</h3>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_msg'];
            unset($_SESSION['success_msg']); ?>
    </div>
    <?php elseif (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_msg'];
            unset($_SESSION['error_msg']); ?>
    </div>
    <?php endif; ?>

    <!-- Add cloth Form -->
    <form action="add_cloths.php" method="POST" enctype="multipart/form-data">


        <div class="form-group">
            <label for="category_id">Category</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($row = mysqli_fetch_assoc($category_result)): ?>
                <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price (Pkr)</label>
            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
        </div>

        <div class="form-group">
            <label for="size">Size</label>
            <select class="form-control" id="size" name="size" required>
                <option value="">Select Size</option>
                <option value="S">Small</option>
                <option value="M">Medium</option>
                <option value="L">Large</option>
                <option value="XL">XL</option>
                <option value="XXL">XXL</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cloth_image">cloth Image</label>
            <input type="file" class="form-control-file" id="cloth_image" name="cloth_image" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn text-white bg-primary">Add cloth Item</button>
        </div>
    </form>
</div>