<?php
include 'header.php';
include '../db.php';

$product_id = $_GET['product_id'] ?? '';

if (!$product_id) {
    echo "<script>alert('Invalid product ID'); window.location.href='view_product.php';</script>";
    exit;
}

// Fetch product details
$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<script>alert('Product not found'); window.location.href='view_product.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Check if new image uploaded
    if (!empty($_FILES['product_image']['name'])) {
        $image_name = $_FILES['product_image']['name'];
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_path = 'uploads/' . basename($image_name);
        move_uploaded_file($image_tmp, $image_path);
    } else {
        $image_path = $product['image_path']; // Keep old image
    }

    // Update query
    $update = "UPDATE products SET 
                product_name='$product_name',
                category='$category',
                brand='$brand',
                price='$price',
                quantity='$quantity',
                image_path='$image_path'
                WHERE product_id='$product_id'";

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='view_product.php';</script>";
    } else {
        echo "<script>alert('Failed to update product.');</script>";
    }
}
?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Edit Product</h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="form">
        <label>Product Name:</label><br>
        <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>"
            required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required><br><br>

        <label>Brand:</label><br>
        <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required><br><br>

        <label>Quntity:</label><br>
        <input type="number" min="1" name="quantity" value="<?= $product['quantity'] ?>" required><br><br>


        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br><br>

        <label>Product Image:</label><br>
        <input type="file" name="product_image" accept="image/*"><br>
        <small>Current Image:</small><br>
        <img src="<?= $product['image_path'] ?>" width="100" style="margin-top:10px; border-radius:5px;"><br><br>

        <div class="btn-div">
            <button type="submit" class="btn">Update</button>
        </div>
    </form>
</div>
</body>

</html>