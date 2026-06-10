<?php
include 'header.php';

require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include database connection

    // Get data from the form
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // SQL query to insert data
    $query = "INSERT INTO products (product_name , price, quantity, category) 
              VALUES ('$product_name', '$price', '$quantity', '$category')";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Success message and redirect
        echo "<script>alert('Product added successfully!'); window.location.href='add_product.php';</script>";
    } else {
        // Error message
        echo "<script>alert('Error adding product. Please try again.'); window.location.href='add_product.php';</script>";
    }

    // Close database connection
    mysqli_close($conn);
}
?>


<div class="container mt-5 rounded shadow borer p-0" style="max-width: 600px;">
    <h3 class="bg-dark text-white text-center p-1">Add New Product</h3>
    <div class="p-3">

        <form action="add_product.php" method="POST">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category">
            </div>
            <div class="text-center mb-3">

                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>
</body>

</html>