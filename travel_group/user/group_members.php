<?php
include 'header.php';
require '../db.php';


$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// Validate group exists
$group_query = "SELECT * FROM groups WHERE id = $group_id";
$group_result = mysqli_query($conn, $group_query);

if (!$group_result || mysqli_num_rows($group_result) !== 1) {
    echo "<script>alert('Group not found.'); window.location.href='view_groups.php';</script>";
    exit;
}

$group = mysqli_fetch_assoc($group_result);

// Fetch approved members
$members_query = "
    SELECT u.user_id, u.name, u.email
    FROM group_requests gr
    JOIN users u ON gr.user_id = u.user_id
    WHERE gr.group_id = $group_id AND gr.status = 'approved'
";

$members_result = mysqli_query($conn, $members_query);
?>

<!DOCTYPE html>
<html>


<div class="container">
    <h2><?php echo htmlspecialchars($group['name']); ?> - Members</h2>

    <?php if (mysqli_num_rows($members_result) > 0): ?>
        <?php while ($member = mysqli_fetch_assoc($members_result)): ?>
            <div class="member">
                <strong><?php echo htmlspecialchars($member['name']); ?></strong><br>
                <?php echo htmlspecialchars($member['email']); ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No approved members found.</p>
    <?php endif; ?>
</div>

</body>

</html>

<?php
mysqli_close($conn);
?>