<?php
// Include the header
include 'header.php';

include '../db_connection.php';


// Fetch feedback for the current cloth
$query = "SELECT f.text, u.user_name, u.email 
          FROM feedback f 
          JOIN users u ON f.user_id = u.user_id";
$stmt = $conn->prepare($query);
$stmt->execute();
$feedback_result = $stmt->get_result();

?>

<!-- Feedback Section -->
<div class="container mt-5">
    <?php if ($feedback_result->num_rows > 0): ?>
    <!-- Feedback Table -->
    <div class="container mt-5 rounded border shadow p-4">
        <h3>Feedback</h3>
        <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white">
                <tr>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($feedback = $feedback_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['email']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($feedback['text'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="container mt-5">
        <p>No feedback yet for this cloth.</p>
    </div>
    <?php endif; ?>
</div>

</body>

</html>