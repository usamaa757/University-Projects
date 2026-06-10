<?php
include 'header.php';
include('../db_connection.php'); // Assuming db_connection.php contains the database connection


// Get the product ID from the URL
$product_id = $_GET['id'];

// Fetch the product details
$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

// If the product doesn't exist, redirect to product list
if (!$product) {
    echo "Product not found.";
    exit;
}

// Process the update if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Update product details in the database
    $update_query = "UPDATE products SET product_name = '$product_name', category = '$category', price = '$price', quantity = '$quantity' WHERE product_id = '$product_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "Product updated successfully.";
        header("Location: manage_products.php"); // Redirect to product management page
        exit;
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}
?>


<!-- Edit Product Form -->
<div class="container mt-5 rounded shadow border p-0" style="max-width: 500px;">
    <h3 class="bg-dark text-center text-white p-2">Edit Product</h3>
    <div class="p-4">


        <form method="POST">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name"
                    value="<?php echo $product['product_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category"
                    value="<?php echo $product['category']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price"
                    value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity"
                    value="<?php echo $product['quantity']; ?>" required>
            </div>
            <div class="text-center">

                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
</body>

</html>