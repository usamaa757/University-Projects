<?php
include 'navbar.php';
include 'db.php';

// ✅ Allow only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

// ✅ Handle approve/reject actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $leave_id = intval($_GET['id']);
    $action = $_GET['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        // Update leave status
        $conn->query("UPDATE leaves SET status = '$action' WHERE id = $leave_id");

        // ✅ If rejected, reset user's availability to default
        if ($action === 'rejected') {
            // Get the user_id of that leave
            $user_result = $conn->query("SELECT user_id FROM leaves WHERE id = $leave_id LIMIT 1");
            if ($user_result && $user_row = $user_result->fetch_assoc()) {
                $user_id = intval($user_row['user_id']);
                // Set default availability (you can change 'Available' to whatever your system’s default is)
                $conn->query("UPDATE users SET availability = 'morning' WHERE id = $user_id");
            }
        }

        $msg = "Leave request has been " . ucfirst($action) . ".";
    }
}

// ✅ Fetch all leave requests with user info
$sql = "
    SELECT l.id, l.start_date, l.end_date, l.reason, l.status, l.created_at,
           u.full_name, u.role
    FROM leaves l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
";
$leaves = $conn->query($sql);
?>

<div class="table-container">
    <h2>Manage Leave Requests</h2>

    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>

    <table>
        <tr>
            <th>User</th>
            <th>Role</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Applied On</th>
            <th>Action</th>
        </tr>

        <?php if ($leaves->num_rows > 0): ?>
            <?php while ($row = $leaves->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td><?= $row['start_date'] ?></td>
                    <td><?= $row['end_date'] ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <a href="?action=approved&id=<?= $row['id'] ?>"
                                onclick="return confirm('Approve this leave request?');">Approve</a> |
                            <a href="?action=rejected&id=<?= $row['id'] ?>" onclick="return confirm('Reject this leave request?');">
                                Reject</a>
                        <?php else: ?>
                            <em>No action</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No leave requests found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>

</html>