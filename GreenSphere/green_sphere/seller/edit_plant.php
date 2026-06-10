<?php
include 'header.php';
include '../db_connection.php';

// Check if ID is set
if (isset($_GET['plant_id'])) {
    $plant_id = $_GET['plant_id'];

    // Fetch the plant details
    $query = "SELECT * FROM plants WHERE plant_id = '$plant_id' AND seller_id = '{$_SESSION['seller_id']}'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $plant = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error_msg'] = "Plant not found.";
        header("Location: edit_plant.php");
        exit();
    }
}

// Handle form submission for updating plant
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plant_name = $_POST['plant_name'];
    $plant_id = $_POST['plant_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

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
                $update_query = "UPDATE plants SET plant_name='$plant_name', price='$price', quantity='$quantity', description='$description', image_url='$target_file' WHERE plant_id='$plant_id' AND seller_id='{$_SESSION['seller_id']}'";
            } else {
                $_SESSION['error_msg'] = "Failed to upload image.";
                header("Location: edit_plant.php?plant_id=$plant_id");
                exit();
            }
        } else {
            $_SESSION['error_msg'] = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: edit_plant.php??plant_id=$plant_id");
            exit();
        }
    } else {
        // Update without changing the image
        $update_query = "UPDATE plants SET plant_name='$plant_name', price='$price', quantity='$quantity', description='$description' WHERE plant_id='$plant_id' AND seller_id='{$_SESSION['seller_id']}'";
    }

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success_msg'] = "Plant updated successfully!";
        header("Location: inventory.php");
        exit();
    } else {
        $_SESSION['error_msg'] = "Failed to update plant.";
    }
}
?>

<!-- HTML Form for Editing Plant -->
<div class="edit-plant-container">
    <h3>Edit Plant</h3>

    <?php if (isset($_SESSION['error_msg'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_msg']; ?>
            <?php unset($_SESSION['error_msg']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_plant.php?id=<?php echo $plant_id; ?>" enctype="multipart/form-data">
        <label for="plant_name">Plant Name:</label>
        <input type="text" id="plant_name" name="plant_name" value="<?php echo $plant['plant_name']; ?>" required>
        <input type="hidden" id="plant_id" name="plant_id" value="<?php echo $plant['plant_id']; ?>">

        <label for="price">Price ($):</label>
        <input type="number" id="price" name="price" value="<?php echo $plant['price']; ?>" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $plant['quantity']; ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"
            required><?php echo $plant['description']; ?></textarea>

        <label for="image_url">Plant Image:</label>
        <?php if (!empty($plant['image_url'])): ?>
            <div>
                <img src="<?php echo $plant['image_url']; ?>" alt="Plant Image" style="width: 100px; height: 100px;">
            </div>
        <?php endif; ?>
        <input type="file" id="image_url" name="image_url" accept=".jpg, .jpeg, .png, .gif">

        <button type="submit" class="btn bg-primary text-white btn-block">Update Listing</button>
    </form>
</div>