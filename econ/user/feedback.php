<?php
include '../db.php';
include 'header.php';

$agent_id = $_GET['agent_id'];
$user_id = $_SESSION['user_id'] ?? null;
$success = "";
$error = "";
// Fetch agent name
$agent_result = $conn->query("SELECT fullname FROM users WHERE id = $agent_id AND role = 'agent'");
$agent = $agent_result->fetch_assoc();
$agent_name = $agent['fullname'] ?? 'Agent';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($user_id && $agent_id && $rating) {
        $stmt = $conn->prepare("INSERT INTO agent_feedback (user_id, agent_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $agent_id, $rating, $comment);
        $stmt->execute();
        $success = "Feedback submitted!";
    } else {
        $error = "All fields are required.";
    }
}
?>


<div class="container">

    <div class="section">
        <div class="section-header">

            <h2>Leave Feedback for <?= htmlspecialchars($agent_name) ?></h2>
        </div>

        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <?php if (!empty($success)): ?><div class="msg"><?= $success ?></div><?php endif; ?>
            <?php if (!empty($error)): ?><div class="msg error"><?= $error ?></div><?php endif; ?>


            <label for="rating">Rating (1 to 5):</label>
            <select name="rating" id="rating" required>
                <option value="">-- Select Rating --</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>" style="color: gold;"><?= str_repeat('★', $i) ?></option>
                <?php endfor; ?>
            </select>

            <label for="comment">Feedback Comment:</label>
            <textarea name="comment" id="comment" rows="4" placeholder="Write your experience..."></textarea>
            <div class="text-center">

                <button type="submit" class="btn">Submit Feedback</button>
            </div>
        </form>
    </div>
</div>
<?php include '../footer.php'; ?>

</body>

</html>