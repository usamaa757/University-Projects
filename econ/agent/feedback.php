<?php
include '../db.php';
include 'header.php';

$agent_id = $_SESSION['user_id'] ?? 0;

// Fetch agent name
$agent_result = $conn->query("SELECT fullname FROM users WHERE id = $agent_id AND role = 'agent'");
$agent = $agent_result->fetch_assoc();
$agent_name = $agent['fullname'] ?? 'Agent';

// Fetch feedback for this agent
$feedbacks = $conn->query("
    SELECT af.rating, af.comment, u.fullname AS user_name, af.created_at
    FROM agent_feedback af
    JOIN users u ON af.user_id = u.id
    WHERE af.agent_id = $agent_id
    ORDER BY af.created_at DESC
");
?>
<div class="section-header">

    <h2>Feedback for <?= htmlspecialchars($agent_name) ?></h2>
</div>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:20px; padding:20px;">
    <?php if ($feedbacks->num_rows > 0): ?>
        <?php while ($row = $feedbacks->fetch_assoc()): ?>
            <div style="width:250px; background:white; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); padding:20px;">
                <div style="font-weight:bold; color:#3AAFA9;"><?= htmlspecialchars($row['user_name']) ?></div>
                <div style="color:#FFD700;">Rating:
                    <?= str_repeat("★", $row['rating']) ?><?= str_repeat("☆", 5 - $row['rating']) ?></div>
                <p style="font-size:14px; color:#333; margin-top:10px;"><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
                <div style="font-size:12px; color:gray; margin-top:10px;"><?= date('F j, Y', strtotime($row['created_at'])) ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color:#555;">No feedback submitted for this agent yet.</p>
    <?php endif; ?>
</div>
<?php include '../footer.php'; ?>