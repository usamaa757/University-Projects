<?php
include("admin_header.php");
include("../db_connection.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all products from the database
$sql = "SELECT * FROM auto_parts";
$result = $conn->query($sql);
?>

<div class="container-fluid mt-3">
    <a href="admin_dashboard.php"><button class="btn btn-primary mb-2">Back to Dashboard</button></a>
    <div class="border shadow-sm rounded">
        <h2 class="text-center bg-dark p-2 text-white">Manage Products</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Part ID</th>
                    <th>Part Name</th>
                    <th>Condition</th>
                    <th>Price</th>
                    <th>Location</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($_GET['msg']) || isset($_GET['error'])): ?>
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_GET['msg']); ?>
                        </div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['part_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['part_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['condition']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['make']); ?></td>
                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                        <td>
                            <?php if ($row['images']) : ?>
                                <img src="<?php echo htmlspecialchars(BASE_PATH . '/seller/uploads/' . $row['images']); ?>" alt="<?php echo htmlspecialchars($row['part_name']); ?>" width="50">
                            <?php else : ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <?php if ($row['status'] == 'show') : ?>
                            <td>
                                <a href="toggle_product.php?part_id=<?php echo $row['part_id']; ?>&action=hide" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to hide this product?');">Hide</a>
                            </td>
                        <?php else : ?>
                            <td>
                                <a href="toggle_product.php?part_id=<?php echo $row['part_id']; ?>&action=show" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to show this product?');">Show</a>
                            </td>
                        <?php endif; ?>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>