<?php
include("config.php");
include("navbar.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = $error = '';
$user_id = $_SESSION['user_id'];

// Upload Furniture
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $desc = trim($_POST['description']);
    $condition = $_POST['condition'];
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);

    // Image Upload handling
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    // Validate fields
    if (!empty($name) && !empty($price) && !empty($image) && !empty($category) && !empty($location)) {

        // Validate image format
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $msg = "Invalid image format! Only JPG, PNG, WEBP allowed.";
        } else {
            // Create a unique image name
            $new_img = uniqid("furn_", true) . "." . $file_ext;

            // Move uploaded image to folder
            move_uploaded_file($tmp_name, "uploads/$new_img");

            // Insert into DB
            $sql = "INSERT INTO furniture (seller_id, name, price, description, condition_status, category, location, image)
                    VALUES ('$user_id', '$name', '$price', '$desc', '$condition', '$category', '$location', '$new_img')";

            if (mysqli_query($conn, $sql)) {
                $msg = "Furniture added successfully!";
            } else {
                $msg = "Error: " . mysqli_error($conn);
            }
        }
    } else {
        $msg = "Please fill all required fields.";
    }
}

// Delete furniture
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM furniture WHERE id=$id AND seller_id='$user_id'");
    $msg = "Furniture deleted successfully!";
}
?>

<div class="form-container">
    <h2>Add Furniture</h2>
    <?php if (!empty($msg)): ?>
        <div class="success-box"><?php echo $msg; ?>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="name" placeholder="Furniture Name" required>

        <input type="number" name="price" placeholder="Price (Pkr)" required>

        <textarea name="description" placeholder="Description"></textarea>

        <select name="condition" required>
            <option value="">Select Condition</option>
            <option value="New">New</option>
            <option value="Good">Good</option>
            <option value="Used">Used</option>
            <option value="Damaged">Damaged</option>
        </select>

        <!-- Category -->
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Sofa">Sofa</option>
            <option value="Chair">Chair</option>
            <option value="Table">Table</option>
            <option value="Bed">Bed</option>
            <option value="Cabinet">Cabinet</option>
            <option value="Bookshelf">Bookshelf</option>
            <option value="Wardrobe">Wardrobe</option>
            <option value="Desk">Desk</option>
            <option value="Dresser">Dresser</option>
        </select>


        <!-- Location / City -->
        <input type="text" name="location" placeholder="City / Location" required>

        <input type="file" name="image" accept="image/*" required>

        <button type="submit" name="add">Add Furniture</button>

    </form>
</div>

</body>

</html>