<?php
include 'header.php';
require '../db.php';

$user_id = $_SESSION['user_id'];

// Fetch private groups where the user is approved
$private_query = "
    SELECT g.id, g.name, g.type
    FROM group_requests gr
    JOIN groups g ON gr.group_id = g.id
    WHERE gr.user_id = $user_id AND gr.status = 'approved'
";

$private_result = mysqli_query($conn, $private_query);
?>

<div class="chat-container">
    <h2>Chats You Can Access</h2>

    <?php

    $hasChats = '';

    if (mysqli_num_rows($private_result) > 0) {
        echo "<h3>Private Groups You've Joined</h3>";
        while ($group = mysqli_fetch_assoc($private_result)) {
            $hasChats = true;
    ?>
    <div class="group-card">
        <strong><?= htmlspecialchars($group['name']) ?></strong>
        <span class="group-type">(<?= htmlspecialchars($group['type']) ?>)</span><br>

        <a class="btn" href="group_members.php?group_id=<?= $group['id'] ?>">Group Members</a>
        <a class="btn" href="group_chat.php?group_id=<?= $group['id'] ?>">Chat</a>
    </div>
    <?php
        }
    }

    if (!$hasChats) {
        echo "<p>You have not joined any private groups, and there are no public groups available.</p>";
    }
    ?>
</div>

<style>
.chat-container {
    padding: 20px;
}

.group-card {
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 15px;
    background: #f8f8f8;
    border-radius: 5px;
}

.btn {
    display: inline-block;
    margin-top: 5px;
    margin-right: 10px;
    padding: 5px 10px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 3px;
}

.btn:hover {
    background-color: #45a049;
}

.group-type {
    font-style: italic;
    color: #888;
}
</style>

</body>

</html>

<?php mysqli_close($conn); ?>