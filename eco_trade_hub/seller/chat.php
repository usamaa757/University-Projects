<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];
$buyer_id = isset($_GET['buyer_id']) ? intval($_GET['buyer_id']) : 0;

if ($buyer_id === 0) {
    echo "Invalid seller.";
    exit();
}

// Fetch messages between the buyer and the selected seller
$sql = "SELECT messages.*, 
               buyers.buyer_name
        FROM messages 
        JOIN sellers ON messages.seller_id = sellers.seller_id 
        JOIN buyers ON messages.buyer_id = buyers.buyer_id 
        WHERE messages.seller_id = ? AND messages.buyer_id = ?
        ORDER BY messages.timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $seller_id, $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
$buyer_name = $result->num_rows > 0 ? $result->fetch_assoc()['buyer_name'] : 'Buyer';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .chat-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            border: 1px solid #ddd;

        }

        .scroll {

            height: 390px;
            /* Adjust this height as needed */
            overflow-y: auto;
            /* Adds vertical scrollbar */
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
</head>

<body>
    <div class="container mt-4">
        <a href="chat_list.php">
            <button class="btn btn-primary">Back</button>
        </a>
        <div class="chat-container">
            <h3 class="text-center heading-bg bg-dark text-white p-2"><?php echo htmlspecialchars($buyer_name); ?></h3>

            <div class="mb-2">

                <div class="scroll p-3">
                    <div id="messageArea">
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <div class="message-bubble <?php echo $row['sender_type'] == 'seller' ? 'message-sent' : 'message-received'; ?>">
                                <p><?php echo htmlspecialchars($row['message']); ?></p>
                                <div class="message-timestamp"><?php echo htmlspecialchars($row['timestamp']); ?></div>
                            </div>
                        <?php endwhile; ?>

                    </div>
                </div>
                <div class="input-text">
                    <form id="messageForm">
                        <div class="input-group">
                            <input type="text" class="form-control" id="messageInput" name="message" placeholder="Type a message" required>
                            <input type="hidden" id="buyer_id" value="<?php echo htmlspecialchars($buyer_id); ?>">
                            <input type="hidden" id="seller_id" value="<?php echo htmlspecialchars($seller_id); ?>">
                            <input type="hidden" id="sender_type" value="seller">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
 
    <script>
        document.getElementById('messageForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var form = event.target;
            var messageInput = document.getElementById('messageInput');
            var message = messageInput.value;
            var buyer_id = document.getElementById('buyer_id').value;
            var seller_id = document.getElementById('seller_id').value;
            var sender_type = document.getElementById('sender_type').value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200 && xhr.responseText.trim() === "Message sent successfully") {
                    var messageBubble = document.createElement('div');
                    messageBubble.className = 'message-bubble ' + (sender_type === 'seller' ? 'message-sent' : 'message-received');
                    messageBubble.innerHTML = '<p>' + message + '</p><div class="message-timestamp">Now</div>';
                    document.getElementById('messageArea').appendChild(messageBubble);
                    messageInput.value = '';
                    document.querySelector('.scroll').scrollTop = document.querySelector('.scroll').scrollHeight; // Scroll to the bottom
                } else {
                    alert("An error occurred while sending the message.");
                }
            };
            xhr.send('message=' + encodeURIComponent(message) + '&seller_id=' + encodeURIComponent(seller_id) + '&buyer_id=' + encodeURIComponent(buyer_id) + '&sender_type=' + encodeURIComponent(sender_type));

        });
    </script>
</body>

</html>