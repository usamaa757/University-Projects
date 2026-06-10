<?php
include 'header.php';
include '../db_connection.php';

// Check if ID is set
if (isset($_GET['cloth_id'])) {
    $cloth_id = $_GET['cloth_id'];

    // Fetch the cloth details
    $query = "
    SELECT *, cat.category_name 
    FROM cloths c 
    JOIN categories cat ON c.category_id = cat.category_id 
    WHERE c.cloth_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cloth_id); // Bind the cloth_id as an integer
    $stmt->execute();

    $result = $stmt->get_result();

    if (mysqli_num_rows($result) == 1) {
        $cloth = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error_msg'] = "cloth not found.";
        header("Location: edit_cloth.php");
        exit();
    }
}

// Handle form submission for updating cloth
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $cloth_id = $_POST['cloth_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $size = $_POST['size'];

    // Check if a new image file is uploaded
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['image_url']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            // Save the uploaded image file
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_file)) {
                // Update query with new image path
                $update_query = "UPDATE cloths SET category_id='$category_id', price='$price', quantity='$quantity', description='$description', size='$size', image_url='$target_file' WHERE cloth_id='$cloth_id' ";
            } else {
                $_SESSION['error_msg'] = "Failed to upload image.";
                header("Location: edit_cloth.php?cloth_id=$cloth_id");
                exit();
            }
        } else {
            $_SESSION['error_msg'] = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: edit_cloth.php?cloth_id=$cloth_id");
            exit();
        }
    } else {
        // Update without changing the image
        $update_query = "UPDATE cloths SET category_id='$category_id', price='$price', quantity='$quantity', description='$description', size='$size' WHERE cloth_id='$cloth_id' ";
    }

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success_msg'] = "Cloth updated successfully!";
        header("Location: cloths_list.php");
        exit();
    } else {
        $_SESSION['error_msg'] = "Failed to update cloth.";
    }
}
?>

<!-- HTML Form for Editing cloth -->
<div class="edit-cloth-container">
    <h3>Edit Cloth</h3>

    <?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_msg']; ?>
        <?php unset($_SESSION['error_msg']); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="edit_cloth.php?cloth_id=<?php echo $cloth_id; ?>" enctype="multipart/form-data">
        <label for="category_name">Type:</label>
        <input type="text" id="category_name" name="category_name"
            value="<?php echo htmlspecialchars($cloth['category_name']); ?>" readonly>

        <!-- Hidden input to send category_id -->
        <input type="hidden" id="category_id" name="category_id"
            value="<?php echo htmlspecialchars($cloth['category_id']); ?>">

        <input type="hidden" id="cloth_id" name="cloth_id" value="<?php echo $cloth['cloth_id']; ?>">

        <label for="price">Price (PKR):</label>
        <input type="number" id="price" name="price" value="<?php echo $cloth['price']; ?>" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $cloth['quantity']; ?>" required>

        <label for="size">Size:</label>
        <input type="text" id="size" name="size" value="<?php echo $cloth['size']; ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"
            required><?php echo $cloth['description']; ?></textarea>

        <label for="image_url">Cloth Image:</label>
        <?php if (!empty($cloth['image_url'])): ?>
        <div class="my-2">
            <img src=" <?php echo $cloth['image_url']; ?>" alt="cloth Image" style="width: 100px; height: 100px;">
        </div>
        <?php endif; ?>
        <input type="file" id="image_url" name="image_url" accept=".jpg, .jpeg, .png, .gif">
        <div class="text-center">

            <button type="submit" class="btn bg-primary text-white">Update Listing</button>
        </div>
    </form>
</div>