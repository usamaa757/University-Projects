<?php
// Include the necessary files
include 'header.php';
include '../db_connection.php';

// Fetch admin's cloth listings
$admin_id = $_SESSION['admin_id'];
$query = "
    SELECT *, cat.category_name
    FROM cloths c
    JOIN categories cat ON c.category_id = cat.category_id";
$result = mysqli_query($conn, $query);
?>
<div class="container mt-5 round border shadow p-3">

    <div class="dashboard-content">

        <div class="listing-management">
            <h3>Your Cloth Listings</h3>
            <a href="add_cloths.php" class="btn text-white bg-primary">Add New Cloth</a>
            <!-- Display success or error messages -->
            <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success mt-3">
                <?php echo $_SESSION['success_msg']; ?>
                <?php unset($_SESSION['success_msg']); ?>
            </div>
            <?php elseif (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_msg']; ?>
                <?php unset($_SESSION['error_msg']); ?>
            </div>
            <?php endif; ?>

            <div class="cloth-listings">

                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="cloth-card">
                    <div class="cloth-image">
                        <?php if (!empty($row['image_url'])): ?>
                        <img src="<?php echo $row['image_url']; ?>" style="height: 150px;" alt="cloth Image">
                        <?php else: ?>
                        <span>No Image</span>
                        <?php endif; ?>
                    </div>
                    <div class="cloth-info">
                        <h4><?php echo $row['category_name']; ?></h4>
                        <p><strong>Price: </strong>PKR <?php echo $row['price']; ?></p>
                        <p><strong>Quantity: </strong><?php echo $row['quantity']; ?></p>
                        <p><strong>Description: </strong><?php echo $row['description']; ?></p>
                        <p><strong>Size: </strong><?php echo $row['size']; ?></p>
                        <div class="actions">
                            <a href="edit_cloth.php?cloth_id=<?php echo $row['cloth_id']; ?>"
                                class="btn btn-info">Edit</a>
                            <a href="delete_cloth.php?cloth_id=<?php echo $row['cloth_id']; ?>"
                                class="btn btn-danger">Delete</a>

                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

            </div>
        </div>
    </div>
</div>
</body>

</html>