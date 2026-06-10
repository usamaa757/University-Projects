<?php
include '../db.php';
include 'header.php';

// Handle art status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $art_id = $_POST['art_id'];
    $new_status = $_POST['update_status'];

    if ($conn->query("UPDATE arts SET status = '$new_status' WHERE art_id = $art_id")) {
        echo "<script>alert('art status updated.'); window.location.href = 'manage_arts.php';</script>";
    } else {
        echo "<script>alert('Failed to update status.'); window.location.href = 'manage_arts.php';</script>";
    }
    exit();
}

// Fetch all arts
$arts = $conn->query("SELECT * FROM arts WHERE status = 'pending' ORDER BY art_id DESC");
?>

<div class="container my-5">
    <div class="card shadow border-0 rounded-4">
        <h3 class="text-center mb-4">Manage Arts</h3>
        <div class="card-body table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Art Name</th>
                        <th>Image</th>

                        <th>Art Desciption</th>

                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($art = $arts->fetch_assoc()): ?>
                    <tr>
                        <td><?= $art['art_name'] ?></td>
                        <td>
                            <?php if (!empty($art['image'])): ?>
                            <img src="<?= htmlspecialchars($art['image']) ?>" alt="Art Image"
                                style="height: 80px; width: auto; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                            <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>

                        <td><?= $art['description'] ?></td>

                        <td>
                            <span
                                class="badge 
                <?= $art['status'] == 'approved' ? 'bg-success' : ($art['status'] == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                <?= ucfirst($art['status']) ?>
                            </span>
                        </td>



                        <td>
                            <form method="post" class="d-flex justify-content-center gap-2">
                                <input type="hidden" name="art_id" value="<?= $art['art_id'] ?>">
                                <button type="submit" name="update_status" value="approved"
                                    class="btn btn-sm">Approve</button>
                                <button type="submit" name="update_status" value="rejected"
                                    class="btn btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if ($arts->num_rows == 0): ?>
                    <tr>
                        <td colspan="5">No arts found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>