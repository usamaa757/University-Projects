<?php
include 'header.php';
include('../db_connection.php'); // Assuming db_connection.php contains the database connection

// Fetch all products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

// Handle delete action if product_id is passed in URL
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete product from the database
    $delete_query = "DELETE FROM products WHERE product_id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Product deleted successfully!'); window.location.href = 'manage_products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product.');</script>";
    }
}
?>


<!-- Product Management Page -->
<div class="container mt-5 rounded shadow border p-0">
    <h3 class="bg-dark text-center text-white p-2">Manage Products</h3>
    <div class="p-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through and display each product
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['product_name']}</td>
                            <td>{$row['category']}</td>
                            <td>Rs. {$row['price']}</td>
                            <td>{$row['quantity']}</td>
                            <td>
                                <a href='edit_product.php?id={$row['product_id']}' class='btn btn-primary'>Edit</a>
                                <a href='manage_products.php?delete_id={$row['product_id']}' class='btn btn-danger' 
                                   onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>