<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];

$sql = "SELECT part_requests.*, buyers.buyer_name, part_requests.status
        FROM part_requests 
        JOIN buyers ON part_requests.buyer_id = buyers.buyer_id 
        ORDER BY part_requests.request_date DESC";
$result = $conn->query($sql);
?>


    <div class="container-fluid mt-3">
        <div class="border shadow bg-white rounded fluid">
            <h3 class="text-center heading-bg bg-dark text-white p-2">Part Requests</h3>
            <div class="p-2">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Buyer Name</th>
                            <th>Part Name</th>
                            <th>Part Description</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Chat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) :  ?>
                           
                            <tr>
                                <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['part_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['part_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                <?php if ($row['status'] == 'pending'): ?>
                                <a href="chat.php?buyer_id=<?php echo $row['buyer_id']; ?>" class="btn btn-sm btn-success">Chat</a>
                                </td>
                            </tr>
                        <?php endif; endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>