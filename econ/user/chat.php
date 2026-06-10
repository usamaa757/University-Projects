<?php
include '../db.php';
include 'header.php';

$current_user = $_SESSION['user_id'];

$chat_with = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $chat_with) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $current_user, $chat_with, $msg);
        $stmt->execute();
    }
}

// Get all users who have chatted with this user
$users_stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.fullname
    FROM users u
    INNER JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE u.id != ?
    AND (m.sender_id = ? OR m.receiver_id = ?)
");
$users_stmt->bind_param("iii", $current_user, $current_user, $current_user);
$users_stmt->execute();
$users_result = $users_stmt->get_result();

// Fetch chat messages
$messages = [];
$chat_name = '';
if ($chat_with) {
    $chat_stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
    $chat_stmt->bind_param("i", $chat_with);
    $chat_stmt->execute();
    $chat_result = $chat_stmt->get_result();
    $chat_user = $chat_result->fetch_assoc();
    $chat_name = $chat_user ? $chat_user['fullname'] : 'Unknown User';

    $conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = $chat_with AND receiver_id = $current_user");

    $msg_stmt = $conn->prepare("SELECT * FROM messages WHERE 
        (sender_id = ? AND receiver_id = ?) OR 
        (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at ASC");
    $msg_stmt->bind_param("iiii", $current_user, $chat_with, $chat_with, $current_user);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();
}
?>

<div class="chat-layout">
    <div class="user-list">
        <h3>Chats</h3>

        <?php while ($user = $users_result->fetch_assoc()): ?>
        <a class="user-item <?= ($user['id'] == $chat_with ? 'active' : '') ?>"
            href="chat.php?user_id=<?= $user['id'] ?>">
            <?= htmlspecialchars($user['fullname']) ?>
        </a>
        <?php endwhile; ?>
    </div>

    <div class="chat-container">
        <?php if ($chat_with): ?>
        <div class="chat-header">Chat with <?= htmlspecialchars($chat_name) ?></div>

        <div class="chat-box">
            <?php while ($row = $messages->fetch_assoc()): ?>
            <div class="message <?= $row['sender_id'] == $current_user ? 'sent' : 'received' ?>">
                <?= htmlspecialchars($row['message']) ?>
            </div>
            <?php endwhile; ?>
        </div>

        <form method="POST">
            <input type="text" name="message" placeholder="Type your message..." required />
            <button type="submit">Send</button>
        </form>
        <?php else: ?>
        <div style="color: #074642; font-size: 1rem;">Select a user to start chatting.</div>
        <?php endif; ?>
    </div>
</div>
<?php include '../footer.php'; ?>