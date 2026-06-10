<?php

include 'header.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Handle image upload
    $image_name = $_FILES['product_image']['name'];
    $image_tmp = $_FILES['product_image']['tmp_name'];
    $image_path = '../asset/images/' . basename($image_name);

    if (move_uploaded_file($image_tmp, $image_path)) {
        $query = "INSERT INTO products (product_name, category, brand, price, quantity, image_path)
                  VALUES ('$product_name', '$category', '$brand', '$price', '$quantity', '$image_path')";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Product added successfully!'); window.location.href='view_product.php';</script>";
        } else {
            echo "<script>alert('Error adding product');</script>";
        }
    } else {
        echo "<script>alert('Image upload failed');</script>";
    }
}

?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Add New Product</h2>
    </div>

    <form method="POST" action="" class="form" enctype="multipart/form-data">
        <label>Product Name:</label><br>
        <input type="text" name="product_name" required placeholder="Product Name"><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" required placeholder="Category"><br><br>

        <label>Brand:</label><br>
        <input type="text" name="brand" required placeholder="Brand"><br><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required placeholder="Price"><br><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" required placeholder="Quantity" min="1"><br><br>
        <!-- Added Quantity field -->

        <label>Product Image:</label><br>
        <input type="file" name="product_image" accept="image/*" required><br><br>

        <div class="btn-div">
            <button type="submit" class="btn" value="Add Product">Add</button>
        </div>
    </form>
</div>
</body>

</html>