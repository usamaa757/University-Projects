<?php
include 'header.php';
require '../db.php';

// Check if it's an admin or user session
$admin_id = $_SESSION['admin_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

if ($group_id <= 0) {
    echo "<script>alert('Invalid group.'); window.location.href='view_groups.php';</script>";
    exit;
}

// Fetch group info
$group_query = "SELECT * FROM groups WHERE id = $group_id";
$group_result = mysqli_query($conn, $group_query);
$group = mysqli_fetch_assoc($group_result);

if (!$group) {
    echo "<script>alert('Group not found.'); window.location.href='view_groups.php';</script>";
    exit;
}

// Access control for private groups (for users only)
if ($group['type'] === 'private' && $user_id && $group['created_by'] != $user_id) {
    $check = "SELECT * FROM group_requests WHERE user_id = $user_id AND group_id = $group_id AND status = 'approved'";
    $result = mysqli_query($conn, $check);
    if (mysqli_num_rows($result) === 0) {
        echo "<script>alert('You are not allowed to view this private group chat.'); window.location.href='view_groups.php';</script>";
        exit;
    }
}

// Handle new message
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    if ($user_id) {
        $insert = "INSERT INTO group_chat (group_id, user_id, message, sender_type) VALUES ($group_id, $user_id, '$message', 'user')";
    } elseif ($admin_id) {
        $insert = "INSERT INTO group_chat (group_id, user_id, message, sender_type) VALUES ($group_id, $admin_id, '$message', 'admin')";
    }

    mysqli_query($conn, $insert);
    header("Location: group_chat.php?group_id=$group_id");
    exit;
}

// Fetch chat messages
$chat_query = "
    SELECT gc.message, gc.created_at, gc.user_id, gc.sender_type,
        CASE 
            WHEN gc.sender_type = 'user' THEN u.name
            WHEN gc.sender_type = 'admin' THEN a.name
        END AS sender_name
    FROM group_chat gc
    LEFT JOIN users u ON gc.sender_type = 'user' AND gc.user_id = u.user_id
    LEFT JOIN admin a ON gc.sender_type = 'admin' AND gc.user_id = a.admin_id
    WHERE gc.group_id = $group_id
    ORDER BY gc.created_at ASC
";
$chat_result = mysqli_query($conn, $chat_query);
?>

<div class="header">
    <h2>Group Chat: <?php echo htmlspecialchars($group['name']); ?></h2>
</div>

<div class="chat-container">
    <a class="back-link" href="view_groups.php">&larr; Back to Groups</a>

    <div class="chat-box">
        <?php if (mysqli_num_rows($chat_result) > 0): ?>
        <?php while ($chat = mysqli_fetch_assoc($chat_result)): ?>
        <?php
                $is_mine = false;
                if ($user_id && $chat['sender_type'] === 'user' && $chat['user_id'] == $user_id) $is_mine = true;
                if ($admin_id && $chat['sender_type'] === 'admin' && $chat['user_id'] == $admin_id) $is_mine = true;
                ?>
        <div class="message <?php echo $is_mine ? 'mine' : 'other'; ?>">
            <div class="bubble">
                <?php if (!$is_mine): ?>
                <span class="name"><?php echo htmlspecialchars($chat['sender_name']); ?>
                    (<?php echo $chat['sender_type']; ?>)</span><br>
                <?php endif; ?>
                <?php echo htmlspecialchars($chat['message']); ?><br>
                <span class="timestamp"><?php echo date('d M Y h:i A', strtotime($chat['created_at'])); ?></span>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No messages yet. Start the conversation!</p>
        <?php endif; ?>
    </div>

    <form method="POST">
        <textarea name="message" placeholder="Type your message here..." required></textarea>
        <div class="text-center">
            <button type="submit" class="btn">Send</button>
        </div>
    </form>
</div>

</body>

</html>

<?php mysqli_close($conn); ?>