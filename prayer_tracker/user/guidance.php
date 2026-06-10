<?php
include 'header.php';
include '../db.php';

// Fetch all guidance content
$result = $conn->query("SELECT * FROM guidance ORDER BY created_at DESC");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">📘 Guidance Center</h3>

    <?php if ($result->num_rows > 0): ?>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><?= htmlspecialchars($row['title']) ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Type:</strong> <?= htmlspecialchars(ucfirst($row['type'])) ?></p>
                    <p><strong>Created At:</strong> <?= date("d M, Y", strtotime($row['created_at'])) ?></p>
                    <p><strong>Content:</strong></p>
                    <?php if ($row['type'] === 'text'): ?>
                    <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <?php else: ?>
                    <a href="<?= htmlspecialchars($row['content']) ?>" target="_blank">Click here to view</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <p class="text-center">No guidance content available at the moment.</p>
    <?php endif; ?>
</div>