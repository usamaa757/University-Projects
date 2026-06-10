<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// Fetch part requests associated with this buyer
$sql = "SELECT * FROM part_requests WHERE buyer_id = ? ORDER BY request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyer_id); // Bind buyer_id as an integer
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-fluid mt-3">
    <div class="border shadow bg-white rounded fluid">
        <h3 class="text-center heading-bg bg-dark text-white p-2">Part Requests</h3>
        <table class="table table-bordered">
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

            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Part Name</th>
                    <th>Description</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): // Check if there are any rows ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['part_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['part_description']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <a href="complete_request.php?request_id=<?php echo $row['request_id']; ?>" class="btn btn-sm btn-success">Complete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-danger">No part requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>
