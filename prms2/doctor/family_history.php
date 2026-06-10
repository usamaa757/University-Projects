<?php
include '../db.php';
include 'sidebar.php';
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    $history_stmt = $conn->prepare("SELECT condition_name, relationship, status, notes, created_at FROM family_history WHERE patient_id = ? ORDER BY created_at DESC");
    $history_stmt->bind_param("i", $patient_id);
    $history_stmt->execute();
    $history_res = $history_stmt->get_result();
}
?>

<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Family History</h2>

        <?php if (isset($history_res) && $history_res->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Condition Name</th>
                        <th>Relationship</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $history_res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['condition_name']); ?></td>
                        <td><?= htmlspecialchars($row['relationship']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['notes'])); ?></td>
                        <td><?= date('d M, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info mt-4">No family history records found for this patient.</div>
        <?php endif; ?>
    </div>
</div>