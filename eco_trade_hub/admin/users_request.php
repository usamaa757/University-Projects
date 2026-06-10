<?php

include("admin_header.php");
include("../db_connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location:  ../login.php?msg=" . urlencode("Please log in as admin first."));
    exit();
}

$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $user_type = $_POST['user_type']; // Identify if the user is a buyer or seller
    $user_id = intval($_POST['user_id']);

    // Determine the correct column name for the ID based on the user type
    $id_column = ($user_type === 'buyers') ? 'buyer_id' : 'seller_id';

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE $user_type SET status = 'approved' WHERE $id_column = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $msg = ucfirst($user_type) . " approved successfully.";
        } else {
            $msg = "Error approving " . $user_type . ".";
        }
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM $user_type WHERE $id_column = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $msg = ucfirst($user_type) . " deleted successfully.";
        } else {
            $msg = "Error deleting " . $user_type . ".";
        }
    }
}

// Fetch pending buyers and sellers
$buyers_result = $conn->query("SELECT * FROM buyers WHERE status = 'pending'");
$sellers_result = $conn->query("SELECT * FROM sellers WHERE status = 'pending'");
?>

<div class="container mt-3">
    <a href="admin_dashboard.php"><button class="btn btn-primary mb-2">Back to Dashboard</button></a>
    <div class="border shadow-sm rounded">
        <h2 class="text-center bg-dark p-2 text-white">User Requests</h2>
        <div class="p-3">
            <?php if ($msg != '') : ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <h4>Buyers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($buyers_result->num_rows > 0) : ?>
                        <?php while ($row = $buyers_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['buyer_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['buyer_id']); ?>">
                                        <input type="hidden" name="user_type" value="buyers">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['buyer_id']); ?>">
                                        <input type="hidden" name="user_type" value="buyers">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td class="text-danger text-center" colspan="4">No pending buyer requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h4>Sellers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sellers_result->num_rows > 0) : ?>
                        <?php while ($row = $sellers_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['seller_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['seller_id']); ?>">
                                        <input type="hidden" name="user_type" value="sellers">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['seller_id']); ?>">
                                        <input type="hidden" name="user_type" value="sellers">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td class="text-danger text-center" colspan="4">No pending seller requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

</html>