<?php
include("config.php");
include("navbar.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$msg = $error = '';
$id = intval($_GET['id']);  // safer to cast as integer
$user_id = $_SESSION['user_id'];

// Fetch furniture item
$result = mysqli_query($conn, "SELECT * FROM furniture WHERE id=$id AND seller_id='$user_id' AND status !='block'");
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "<script>alert('Invalid furniture item!'); window.location='furniture_list.php';</script>";
    exit();
}

// Predefined categories
$categories = ['Sofa', 'Chair', 'Table', 'Bed', 'Cabinet', 'Wardrobe', 'Desk', 'Other'];

// Handle update
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $desc = trim($_POST['description']);
    $condition = $_POST['condition'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    // Image upload handling
    $image_update = "";
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_img = uniqid("furn_", true) . "." . $file_ext;
            move_uploaded_file($tmp_name, "uploads/$new_img");
            $image_update = ", image='$new_img'";
        } else {
            $error = "Invalid image format! Only JPG, PNG, WEBP allowed.";
        }
    }

    // Update furniture
    if (empty($error)) {
        $sql = "UPDATE furniture 
                SET name='$name', price='$price', description='$desc', condition_status='$condition', category='$category', location='$location' 
                $image_update
                WHERE id=$id AND seller_id='$user_id'";
        if (mysqli_query($conn, $sql)) {
            $msg = "Furniture updated successfully!";
            // Refresh item info
            $result = mysqli_query($conn, "SELECT * FROM furniture WHERE id=$id AND seller_id='$user_id'");
            $item = mysqli_fetch_assoc($result);
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="form-container">
    <h2>Edit Furniture</h2>

    <?php if (!empty($msg)): ?>
        <div class="success-box"><?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
        <input type="number" name="price" value="<?php echo $item['price']; ?>" required>
        <textarea name="description"
            placeholder="Description"><?php echo htmlspecialchars($item['description']); ?></textarea>

        <label>Condition:</label>
        <select name="condition" required>
            <option value="">Select Condition</option>
            <option value="New" <?php if ($item['condition_status'] == 'New') echo 'selected'; ?>>New</option>
            <option value="Good" <?php if ($item['condition_status'] == 'Good') echo 'selected'; ?>>Good</option>
            <option value="Used" <?php if ($item['condition_status'] == 'Used') echo 'selected'; ?>>Used</option>
            <option value="Damaged" <?php if ($item['condition_status'] == 'Damaged') echo 'selected'; ?>>Damaged
            </option>
        </select>

        <label>Category:</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <?php
            foreach ($categories as $cat) {
                $selected = ($item['category'] == $cat) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($cat) . "' $selected>" . htmlspecialchars($cat) . "</option>";
            }
            ?>
        </select>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($item['location']); ?>"
            placeholder="Enter location" required>

        <label>Current Image:</label><br>
        <?php if (!empty($item['image'])): ?>
            <img src="uploads/<?php echo $item['image']; ?>" width="120" height="120"
                style="object-fit:cover; border-radius:5px;">
        <?php else: ?>
            <p>No image uploaded</p>
        <?php endif; ?>
        <br>
        <label>Change Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" name="update">Update</button>
    </form>
</div>