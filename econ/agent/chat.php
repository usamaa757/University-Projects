<?php
include '../db.php';
include 'header.php';

$agent_id = $_SESSION['user_id'];
$chat_with = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$chat_name = '';
$messages = [];

// Fetch all users who chatted with agent
$user_stmt = $conn->prepare("
    SELECT u.id, u.fullname,
        SUM(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count,
        MAX(m.sent_at) AS last_message_time
    FROM users u
    INNER JOIN messages m ON m.sender_id = u.id AND m.receiver_id = ?
    GROUP BY u.id
    ORDER BY last_message_time DESC
");
$user_stmt->bind_param("ii", $agent_id, $agent_id);
$user_stmt->execute();
$user_list = $user_stmt->get_result();

// Fetch chat and messages
if ($chat_with) {
    // Get name of chat partner
    $name_stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
    $name_stmt->bind_param("i", $chat_with);
    $name_stmt->execute();
    $chat_name = $name_stmt->get_result()->fetch_assoc()['fullname'] ?? 'User';

    // Mark messages as read
    $conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = $chat_with AND receiver_id = $agent_id");

    // Fetch chat messages
    $msg_stmt = $conn->prepare("
        SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at ASC
    ");
    $msg_stmt->bind_param("iiii", $agent_id, $chat_with, $chat_with, $agent_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();
}
?>

<div class="chat-layout">
    <div class="user-list">
        <h3>Chats</h3>
        <?php while ($user = $user_list->fetch_assoc()): ?>
        <a class="user-item <?= ($user['id'] == $chat_with ? 'active' : '') ?>"
            href="chat.php?user_id=<?= $user['id'] ?>">
            <?= htmlspecialchars($user['fullname']) ?>
            <?php if ($user['unread_count'] > 0): ?>
            <span class="badge"><?= $user['unread_count'] ?></span>
            <?php endif; ?>
        </a>
        <?php endwhile; ?>
    </div>

    <div class="chat-container">
        <?php if ($chat_with): ?>
        <div class="chat-header">Chat with <?= htmlspecialchars($chat_name) ?></div>

        <div class="chat-box">
            <?php while ($row = $messages->fetch_assoc()): ?>
            <div class="message <?= $row['sender_id'] == $agent_id ? 'sent' : 'received' ?>">
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

<?php
// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_GET['user_id'])) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $agent_id, $chat_with, $msg);
        $stmt->execute();
        header("Location: chat.php?user_id=$chat_with");
        exit;
    }
}
?>