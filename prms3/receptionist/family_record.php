<?php
include 'header.php';

include '../config/database.php';
$patient_id = $_GET['patient_id'];
$history_stmt = $conn->prepare("SELECT * FROM family_history WHERE patient_id = ? ORDER BY created_at DESC");
$history_stmt->bind_param("i", $patient_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<div class="container mt-5 border rounded shadow p-4">
    <h3 class="text-center">My Family History Records</h3>
    <?php if ($history_result->num_rows > 0): ?>
    <?php while ($row = $history_result->fetch_assoc()): ?>
    <div class="card mb-2">
        <div class="card-body">
            <strong><?= htmlspecialchars($row['relation']) ?>: <?= htmlspecialchars($row['relative_name']) ?></strong>
            <p><?= nl2br(htmlspecialchars($row['status'])) ?></p>
            <small class="text-muted">Added on <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></small>
        </div>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="alert alert-info">No family history records yet.</div>
    <?php endif; ?>
</div>