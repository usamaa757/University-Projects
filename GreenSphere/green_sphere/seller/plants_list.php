<?php
// Include the necessary files
include 'header.php';
include '../db_connection.php';


// Fetch seller's plant listings
$seller_id = $_SESSION['seller_id'];
$query = "SELECT * FROM plants WHERE seller_id = '$seller_id'";
$result = mysqli_query($conn, $query);

?>
<div class="container mt-5 round border shadow p-3">

    <div class="dashboard-content">

        <div class="listing-management">
            <h3>Your Plant Listings</h3>
            <a href="add_plants.php" class="btn text-white bg-primary">Add New Plant</a>
            <!-- Display success or error messages -->
            <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_msg']; ?>
                <?php unset($_SESSION['success_msg']); ?>
            </div>
            <?php elseif (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_msg']; ?>
                <?php unset($_SESSION['error_msg']); ?>
            </div>
            <?php endif; ?>


            <div class="plant-listings">

                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="plant-card">
                    <div class="plant-image">
                        <?php if (!empty($row['image_url'])): ?>
                        <img src="<?php echo $row['image_url']; ?>" style="height: 150px;" alt="Plant Image">
                        <?php else: ?>
                        <span>No Image</span>
                        <?php endif; ?>
                    </div>
                    <div class="plant-info">
                        <h4><?php echo $row['plant_name']; ?></h4>
                        <p><strong>Price: </strong>$<?php echo $row['price']; ?></p>
                        <p><strong>Quantity: </strong><?php echo $row['quantity']; ?></p>
                        <p><strong>Description: </strong><?php echo $row['description']; ?></p>
                        <div class="actions">
                            <a href="edit_plant.php?plant_id=<?php echo $row['plant_id']; ?>"
                                class="btn btn-info ">Edit</a>
                            <a href="delete_plant.php?plant_id=<?php echo $row['plant_id']; ?>" a
                                class="btn btn-danger">Delete</a><a
                                href="feedback.php?plant_id=<?php echo $row['plant_id']; ?>"
                                class="btn btn-success">Reviews</a>
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

<style>

</style>