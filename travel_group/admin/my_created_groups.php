<?php
include 'header.php';
require '../db.php';

$user_id = $_SESSION['admin_id'];

// Fetch groups created by the user
$groups_query = "
    SELECT * FROM groups 
    WHERE created_by = $user_id 
    ORDER BY created_at DESC
";

$groups_result = mysqli_query($conn, $groups_query);
?>

<div class="container">
    <h2>My Created Groups</h2>

    <?php if ($groups_result && mysqli_num_rows($groups_result) > 0): ?>
    <?php while ($group = mysqli_fetch_assoc($groups_result)): ?>
    <div class="group">
        <h3><?php echo htmlspecialchars($group['name']); ?></h3>
        <p><strong>Type:</strong> <?php echo ucfirst($group['type']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($group['description']); ?></p>
        <div class="actions">
            <a href="group_chat.php?group_id=<?php echo $group['id']; ?>">Go to Chat</a>
            <a href="delete_group.php?group_id=<?php echo $group['id']; ?>"
                onclick="return confirm('Are you sure you want to delete this group? This will remove all associated messages and requests.')">Delete
                Group</a>

            <a href="group_members.php?group_id=<?php echo $group['id']; ?>">View Members</a>
        </div>

        <h4>Join Requests:</h4>
        <?php
                $group_id = $group['id'];
                $requests_query = "
                            SELECT gr.id AS request_id, u.name, u.email 
                            FROM group_requests gr 
                            JOIN users u ON gr.user_id = u.user_id 
                            WHERE gr.group_id = $group_id AND gr.status = 'pending'
                        ";
                $requests_result = mysqli_query($conn, $requests_query);
                ?>

        <?php if ($requests_result && mysqli_num_rows($requests_result) > 0): ?>
        <ul>
            <?php while ($request = mysqli_fetch_assoc($requests_result)): ?>
            <li>
                <?php echo htmlspecialchars($request['name']); ?> (<?php echo $request['email']; ?>)

                <div class="actions">
                    <a href="handle_request.php?action=approve&id=<?php echo $request['request_id']; ?>">Approve</a>
                    <a href="handle_request.php?action=reject&id=<?php echo $request['request_id']; ?>">Reject</a>

                </div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>No pending requests.</p>
        <?php endif; ?>

    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <p>You haven't created any groups yet.</p>
    <?php endif; ?>
</div>

</body>

</html>

<?php mysqli_close($conn); ?>