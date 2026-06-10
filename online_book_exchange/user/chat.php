<?php
include 'header.php';
include '../db_connection.php';
$receiver_id = $_GET['user_id'];
$sender_id = $_SESSION['user_id'];

// Fetch the chat partner's information
$query = "SELECT user_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$stmt->bind_result($chat_user_name);
$stmt->fetch();
$stmt->close();

// Fetch messages exchanged between the logged-in user and the receiver (chat partner)
$query = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
?>
<style>
    /* Chat styles */
    .chat-container {
        max-width: 600px;
        margin: 0 auto;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .scroll {
        height: 390px;
        overflow-y: auto;
    }

    .message-bubble {
        padding: 10px;
        margin: 10px 0;
        border-radius: 10px;
        max-width: 80%;
        word-wrap: break-word;
    }

    .message-sent {
        background-color: #dcf8c6;
        text-align: right;
        margin-left: auto;
    }

    .message-received {
        background-color: #fff;
        text-align: left;
        margin-right: auto;
    }

    .message-timestamp {
        font-size: 0.8em;
        color: #666;
    }
</style>
<div class="chat-container mt-3">
    <h3 class="text-center bg-dark text-white p-2"><?php echo htmlspecialchars($chat_user_name); ?></h3>
    <div class="mb-2">
        <div class="scroll p-3">
            <div id="messageArea">
                <?php foreach ($messages as $row) : ?>
                    <div
                        class="message-bubble <?php echo $row['sender_id'] == $sender_id ? 'message-sent' : 'message-received'; ?>">
                        <p><?php echo htmlspecialchars($row['message']); ?></p>
                        <div class="message-timestamp"><?php echo htmlspecialchars($row['timestamp']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="input-text">
            <form id="messageForm">
                <div class="input-group">
                    <input type="text" class="form-control" id="messageInput" name="message"
                        placeholder="Type a message" required>
                    <input type="hidden" id="sender_id" value="<?php echo htmlspecialchars($sender_id); ?>">
                    <input type="hidden" id="receiver_id" value="<?php echo htmlspecialchars($receiver_id); ?>">
                    <!-- Receiver ID for chat -->
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();

        var messageInput = document.getElementById('messageInput');
        var message = messageInput.value;
        var sender_id = document.getElementById('sender_id').value;
        var receiver_id = document.getElementById('receiver_id').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_message.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            console.log(xhr.responseText); // Log the response to see what's being returned
            if (xhr.status === 200 && xhr.responseText === "Message sent successfully") {
                var messageBubble = document.createElement('div');
                messageBubble.className = 'message-bubble message-sent';
                messageBubble.innerHTML = '<p>' + message + '</p><div class="message-timestamp">Now</div>';
                document.getElementById('messageArea').appendChild(messageBubble);
                messageInput.value = '';
                document.querySelector('.scroll').scrollTop = document.querySelector('.scroll').scrollHeight;
            } else {
                alert("An error occurred while sending the message: " + xhr.responseText);
            }
        };

        xhr.send('message=' + encodeURIComponent(message) + '&sender_id=' + encodeURIComponent(sender_id) +
            '&receiver_id=' + encodeURIComponent(receiver_id)); // Sending receiver_id
    });
</script>