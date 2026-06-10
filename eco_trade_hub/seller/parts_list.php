<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];

// Fetch auto parts associated with this seller
$sql = "SELECT * FROM auto_parts WHERE seller_id = ? ORDER BY created_at ASC";
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
                   
                    </tr>
                </thead>
                <tbody>
                

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
                            
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    </body>

    </html>