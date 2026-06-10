<?php
include '../db.php';
include 'header.php';

// Handle reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply']) && isset($_POST['id'])) {
    $reply = trim($_POST['reply']);
    $id = (int)$_POST['id'];
    if ($reply !== '') {
        $stmt = $conn->prepare("UPDATE contact_messages SET reply = ? WHERE id = ?");
        $stmt->bind_param("si", $reply, $id);
        $stmt->execute();
    }
}

// Get user list
$userList = $conn->query("SELECT id, fullname, email FROM contact_messages ORDER BY id DESC");

// If a user is selected
$selected_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selected_message = null;
if ($selected_id) {
    $msg_stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $msg_stmt->bind_param("i", $selected_id);
    $msg_stmt->execute();
    $selected_message = $msg_stmt->get_result()->fetch_assoc();
}
?>

<style>
.contact-message-layout {
    display: flex;
    height: 80vh;
    border: 1px solid #ccc;
    border-radius: 10px;
    overflow: hidden;
    font-family: Arial, sans-serif;
}

/* Left panel: User list */
.user-list {
    width: 30%;
    background-color: #f9f9f9;
    border-right: 1px solid #ddd;
    padding: 20px;
    overflow-y: auto;
}

.user-list h3 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #074642;
}

.user-list ul {
    list-style: none;
    padding-left: 0;
    margin: 0;
}

.user-list a {
    display: block;
    padding: 10px;
    border-radius: 5px;
    text-decoration: none;
    margin-bottom: 10px;
    background-color: #fff;
    color: #333;
    transition: background 0.3s;
}

.user-list a:hover,
.user-list a.active {
    background-color: #e0f7f1;
    color: #074642;
    font-weight: bold;
}

/* Right panel: Message & reply */
.message-view {
    width: 70%;
    padding: 20px;
    overflow-y: auto;
}

.message-view h3 {
    color: #074642;
    margin-bottom: 10px;
}

.message-view p {
    margin: 10px 0;
    line-height: 1.5;
}

.message-view textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    resize: vertical;
    font-size: 14px;
}

.message-view button {
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #074642;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.message-view button:hover {
    background-color: #06655a;
}
</style>

<div class="contact-message-layout">
    <!-- User list -->
    <div class="user-list">
        <div class="section-header">

            <h3>Users</h3>
        </div>
        <ul>
            <?php while ($user = $userList->fetch_assoc()): ?>
            <li>
                <a class="<?= $selected_id == $user['id'] ? 'active' : '' ?>" href="?id=<?= $user['id'] ?>">
                    <strong><?= htmlspecialchars($user['fullname']) ?></strong><br>
                    <small><?= htmlspecialchars($user['email']) ?></small>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Message view -->
    <div class="message-view">
        <?php if ($selected_message): ?>
        <h3>Message from <?= htmlspecialchars($selected_message['fullname']) ?></h3>
        <p><strong>Email:</strong> <?= htmlspecialchars($selected_message['email']) ?></p>
        <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($selected_message['message'])) ?></p>

        <form method="POST">
            <input type="hidden" name="id" value="<?= $selected_message['id'] ?>">
            <label for="reply"><strong>Reply:</strong></label><br>
            <textarea name="reply" rows="5"><?= htmlspecialchars($selected_message['reply']) ?></textarea><br>
            <button type="submit">Send Reply</button>
        </form>
        <?php else: ?>
        <p>Please select a user from the list to view their message.</p>
        <?php endif; ?>
    </div>
</div><?php include '../footer.php'; ?>