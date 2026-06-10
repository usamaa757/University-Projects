<?php
include 'header.php';
require '../db.php';

$user_id = $_SESSION['user_id'];

// Handle search/filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Base query (created_by now refers to admin.id)
$query = "
    SELECT g.*, a.name AS creator_name 
    FROM groups g 
    JOIN admin a ON g.created_by = a.admin_id 
    WHERE 
        g.id NOT IN (
            SELECT group_id 
            FROM group_requests 
            WHERE user_id = $user_id
        )
";

// Search and filter
if (!empty($search)) {
    $query .= " AND g.name LIKE '%$search%'";
}

if (!empty($type) && ($type == 'public' || $type == 'private')) {
    $query .= " AND g.type = '$type'";
}

$query .= " ORDER BY g.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <form method="get" class="search-form">
        <input type="text" name="search" placeholder="Search by group name"
            value="<?php echo htmlspecialchars($search); ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="public" <?php if ($type == 'public') echo 'selected'; ?>>Public</option>
            <option value="private" <?php if ($type == 'private') echo 'selected'; ?>>Private</option>
        </select>
        <button type="submit" class="btn">Search</button>
    </form>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <?php while ($group = mysqli_fetch_assoc($result)): ?>
    <div class="group">
        <h3><?php echo htmlspecialchars($group['name']); ?></h3>
        <p><strong>Type:</strong> <?php echo ucfirst($group['type']); ?></p>
        <?php if ($group['type'] === 'public'): ?>
        <p><strong>Created by Admin:</strong> <?php echo htmlspecialchars($group['creator_name']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($group['description']); ?></p>
        <p><strong>Created at:</strong> <?php echo htmlspecialchars($group['created_at']); ?></p>
        <?php endif; ?>

        <div class="actions">
            <?php if ($group['type'] === 'public'): ?>
            <a href="group_chat.php?group_id=<?php echo $group['id']; ?>">Group Chat</a>
            <a href="request_join.php?group_id=<?php echo $group['id']; ?>">Request to Join</a>
            <?php else: ?>
            <a href="request_join.php?group_id=<?php echo $group['id']; ?>">Request to Join</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <p>No groups available to join.</p>
    <?php endif; ?>
</div>

</body>

</html>

<?php mysqli_close($conn); ?>