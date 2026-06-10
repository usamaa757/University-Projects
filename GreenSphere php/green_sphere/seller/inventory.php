<?php
include 'header.php';
include '../db_connection.php';
// Fetch plants listed by the seller
$plants_query = "SELECT * FROM plants WHERE seller_id = '$seller_id'";
$plants_result = mysqli_query($conn, $plants_query);
?>
<!-- Plant Inventory Management -->
<div class="container mt-5 round border shadow">
    <div class="card-body">
        <h3>Your Inventory</h3>
        <a href="add_plants.php" class="btn btn-success mb-3">Add New Plant</a>
        <table class="table">
            <!-- Display success or error messages -->
            <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_msg']; ?>
                <?php unset($_SESSION['success_msg']); ?>
            </div>

            <?php endif; ?>
            <thead class="bg-primary text-white">
                <tr>
                    <th>Plant ID</th>
                    <th>Plant Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($plants_result) > 0) {
                    while ($plant = mysqli_fetch_assoc($plants_result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($plant['plant_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($plant['plant_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($plant['plant_type']) . "</td>";
                        echo "<td>$" . htmlspecialchars($plant['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($plant['quantity']) . "</td>";
                        echo "<td>
                                    <a href='edit_plant.php?plant_id=" . htmlspecialchars($plant['plant_id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_plant.php?plant_id=" . htmlspecialchars($plant['plant_id']) . "' class='btn btn-danger btn-sm'>Delete</a>
                                  </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No plants listed yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>