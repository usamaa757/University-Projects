<?php
include("../db_connection.php");
include("header.php");

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch distinct chat partners (users who have chatted with the logged-in user)
$sql = "SELECT DISTINCT u.user_id, u.user_name
        FROM messages m
        JOIN users u ON m.receiver_id = u.user_id
        WHERE m.sender_id = ?
        UNION
        SELECT DISTINCT u.user_id, u.user_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.receiver_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4 round border shadow p-0">
    <div class=" text-center bg-dark text-white">
        <h3 class="mb-4 p-2">Chat List</h3>
    </div>
    <div class="p-3">
        <ul class="list-group">
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <li class="list-group-item">
                <a href="chat.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>">
                    <?php echo htmlspecialchars($row['user_name']); ?>
                </a>
            </li>
            <?php endwhile; ?>
            <?php else: ?>
            <li class="list-group-item">No chats found</li>
            <?php endif; ?>
        </ul>
    </div>
</div>