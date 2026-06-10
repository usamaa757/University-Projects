<?php
session_start();
require_once '../db_connection.php';



$staff_id = 1;

// Fetch messages between the logged-in user and the other user
$sql = "SELECT m.message_id, m.message_text, m.created_at, u.name AS sender, m.sender_id
        FROM messages m
        JOIN staff u ON m.sender_id = u.staff_id
        WHERE (m.sender_id = ? OR m.receiver_id = ?) AND (m.receiver_id = ? OR m.sender_id = ?)
        ORDER BY m.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $staff_id, $staff_id, $staff_id, $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $user_id, $receiver_id, $message_text);
$stmt->execute();

header("Location: send_msg.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inbox</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }

    .chat-container {
        width: 80%;
        margin: 0 auto;
        padding: 10px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        max-height: 400px;
    }

    .message {
        display: flex;
        margin-bottom: 10px;
    }

    .message .sender {
        font-weight: bold;
        margin-right: 5px;
    }

    .message .text {
        background-color: #e1f7d5;
        padding: 10px;
        border-radius: 10px;
        max-width: 80%;
    }

    .message.sent .text {
        background-color: #c1e1f7;
        margin-left: auto;
    }

    .message.received .text {
        background-color: #e1f7d5;
        margin-right: auto;
    }

    .message .timestamp {
        font-size: 12px;
        color: #888;
        margin-top: 5px;
    }

    .send-message {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .send-message textarea {
        width: 85%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .send-message button {
        padding: 10px;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    .send-message button:hover {
        background-color: #45a049;
    }
    </style>
</head>

<body>
    <h2>Your Messages</h2>

    <div class="chat-container">
        <?php while ($row = $result->fetch_assoc()): ?>
        <?php
            $message_class = ($row['sender_id'] == $staff_id) ? 'sent' : 'received';
            $formatted_time = date('d M, Y h:i A', strtotime($row['created_at']));
            ?>
        <div class="message <?= $message_class ?>">
            <span class="sender"><?= htmlspecialchars($row['sender']) ?>:</span>
            <div class="text"><?= htmlspecialchars($row['message_text']) ?></div>
            <div class="timestamp"><?= $formatted_time ?></div>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="send-message">
        <form method="post" action="send_msg.php">
            <textarea name="message_text" rows="3" placeholder="Type your message..." required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>

</html>