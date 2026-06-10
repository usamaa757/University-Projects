<?php
include 'header.php';
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $plant_name = mysqli_real_escape_string($conn, $_POST['plant_name']);
    $plant_type = mysqli_real_escape_string($conn, $_POST['plant_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    $seller_id = $_SESSION['seller_id'];  // Assuming this is stored in the session

    // Handle image upload
    if (isset($_FILES['plant_image']) && $_FILES['plant_image']['error'] == 0) {
        $target_dir = "uploads/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($_FILES['plant_image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type (only allow jpg, jpeg, png, gif)
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error_msg'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: add_plant.php");
            exit();
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['plant_image']['tmp_name'], $target_file)) {
            // File uploaded successfully, continue
            $image_path = $target_file;
        } else {
            $_SESSION['error_msg'] = "There was an error uploading the image.";
            header("Location: add_plant.php");
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Please upload a valid image.";
        header("Location: add_plant.php");
        exit();
    }

    // Insert data into the database
    $query = "INSERT INTO plants (seller_id, plant_name, plant_type, description, price, quantity, image_url) 
              VALUES ('$seller_id', '$plant_name', '$plant_type', '$description', '$price', '$quantity', '$image_path')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_msg'] = "Plant added successfully!";
    } else {
        $_SESSION['error_msg'] = "Error: " . mysqli_error($conn);
    }
}

// Close the connection
mysqli_close($conn);
?>

<div class="container mt-5 round border shadow p-3" style="max-width: 600px;">
    <h3>Add New Plant</h3>

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

    <!-- Add Plant Form -->
    <form action="add_plants.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="plant_name">Plant Name</label>
            <input type="text" class="form-control" id="plant_name" name="plant_name" required>
        </div>

        <div class="form-group">
            <label for="plant_type">Plant Type</label>
            <select class="form-control" id="plant_type" name="plant_type" required>
                <option value="">Select Type</option>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
                <option value="Flowering">Flowering Plants</option>
                <option value="Herbal">Herbal Plants</option>
                <option value="Succulent">Succulents</option>
                <option value="Aquatic">Aquatic Plants</option>
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
            <label for="plant_image">Plant Image</label>
            <input type="file" class="form-control-file" id="plant_image" name="plant_image" required>
        </div>
        <div class="text-center">

            <button type="submit" class="btn text-white bg-primary">Add Plant</button>
        </div>
    </form>
</div>