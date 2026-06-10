<?php
include('header.php');
include('../db_connection.php');

// Fetch all products to populate the product dropdown
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $sale_date = date('Y-m-d'); // Current date
    $price_query = "SELECT price FROM products WHERE product_id = '$product_id'";
    $price_result = mysqli_query($conn, $price_query);
    $price_row = mysqli_fetch_assoc($price_result);
    $price = $price_row['price'];
    $total_amount = $price * $quantity;

    // Insert the sale record into the database
    $insert_query = "INSERT INTO sales (product_id, quantity, sale_date, total_amount) 
                     VALUES ('$product_id', '$quantity', '$sale_date', '$total_amount')";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Sale recorded successfully!');</script>";
    } else {
        echo "<script>alert('Error recording sale.');</script>";
    }
}
?>

<!-- Sale Recording Form -->
<div class="container mt-5 rounded shadow border p-0" style="max-width: 500px;">
    <h3 class="bg-dark text-center text-white p-2">Record A Sale</h3>
    <div class="p-4">

        <form method="POST" action="add_sale.php">
            <div class="form-group">
                <label for="product_id">Product</label>
                <select class="form-control" id="product_id" name="product_id" required>
                    <option value="">Select a Product</option>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <option value="<?php echo $row['product_id']; ?>">
                        <?php echo $row['product_name']; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity Sold</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <button type="submit" class="btn btn-primary">Record Sale</button>
        </form>
    </div>
</div>
</body>

</html>