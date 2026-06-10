<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];

// Fetch auto parts associated with this seller
$sql = "SELECT * FROM auto_parts WHERE seller_id = ? AND status= 'show' ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container-fluid mt-3">
    <div class="border shadow bg-white rounded fluid">
        <h3 class="text-center heading-bg bg-dark text-white p-2">Auto Parts</h3>
        <div class="p-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Part ID</th>
                        <th>Part Name</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Price</th>
                        <th>Location</th>
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
                            <td><?php echo htmlspecialchars($row['make']); ?></td>
                            <td><?php echo htmlspecialchars($row['model']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>

                            <td>
                                <?php if ($row['images']) : ?>
                                    <img src="<?php echo htmlspecialchars(BASE_PATH . '/seller/uploads/' . $row['images']); ?>" alt="<?php echo htmlspecialchars($row['part_name']); ?>" width="50">
                                    <?php else : ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_auto_part.php?part_id=<?php echo $row['part_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="delete_auto_part.php?part_id=<?php echo $row['part_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this part?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    </body>

    </html>