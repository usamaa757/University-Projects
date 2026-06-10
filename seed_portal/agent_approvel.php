<?php
include 'header.php';
include 'db_connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Approve agent
if (isset($_GET['approve'])) {
    $agent_id = intval($_GET['approve']);
    $conn->query("UPDATE agents SET status='Approved' WHERE id='$agent_id'");
    $message = "<p style='color:green;'>Agent approved successfully</p>";
}

// Reject agent
if (isset($_GET['reject'])) {
    $agent_id = intval($_GET['reject']);
    $conn->query("UPDATE agents SET status='Rejected' WHERE id='$agent_id'");
    $message = "<p style='color:red;'>Agent rejected</p>";
}

// Fetch all agents
$result = $conn->query("SELECT * FROM agents ORDER BY status DESC, id ASC");
?>

<div class="container" style="width:90%">
    <h2>👨‍🌾 Agent Approval Panel</h2>
    <?php if (isset($message)) echo $message; ?>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Organization</th>
            <th>Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['contact']); ?></td>
            <td><?php echo htmlspecialchars($row['organization']); ?></td>
            <td><?php echo htmlspecialchars($row['type']); ?></td>
            <td>
                <?php
                        if ($row['status'] == 'Approved') {
                            echo "<span style='color:green;font-weight:bold;'>Approved</span>";
                        } elseif ($row['status'] == 'Pending') {
                            echo "<span style='color:orange;font-weight:bold;'>Pending</span>";
                        } else {
                            echo "<span style='color:red;font-weight:bold;'>Rejected</span>";
                        }
                        ?>
            </td>
            <td>
                <?php if ($row['status'] == 'Pending'): ?>
                <a href="?approve=<?php echo $row['id']; ?>" style="color:green;">Approve</a> |
                <a href="?reject=<?php echo $row['id']; ?>" style="color:red;">Reject</a>
                <?php else: ?>
                <em>No action</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <p>No agents registered yet.</p>
    <?php endif; ?>
</div>

</body>

</html>