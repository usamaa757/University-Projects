<?php
include 'header.php';
include '../db.php';

$query = "SELECT * FROM products"; // Replace with your actual table name
$result = mysqli_query($conn, $query);
?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Manage Products & Services</h2>
        <a href="add_product.php" class="btn">+ Add
            New</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Price</th>
                <th>quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Product Image" class="product-img">
                        </td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['brand']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><?= htmlspecialchars($row['quantity']) ?></td>

                        <td>
                            <a href="edit_product.php?product_id=<?= $row['product_id'] ?>" class="edit_button">Edit</a>
                            <a href="delete_product.php?product_id=<?= $row['product_id'] ?>" class="delete_button"
                                onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>

</html>