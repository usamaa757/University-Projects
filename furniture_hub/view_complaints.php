<?php
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$admin_id = $_SESSION['user_id'];
$msg = '';

// Handle reply or status update
if (isset($_POST['update_complaint'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $reply_text = mysqli_real_escape_string($conn, $_POST['reply_text']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE complaints 
                         SET reply='$reply_text', status='$status', replied_at=NOW() 
                         WHERE id='$complaint_id'");
    $msg = "Complaint updated successfully!";
}

// Fetch all complaints
$result = mysqli_query($conn, "SELECT c.*, u.name AS buyer_name, fur.name AS furniture_name, 
                               fur.image AS furniture_image
                               FROM complaints c
                               JOIN orders o ON c.order_id = o.id
                               JOIN users u ON c.buyer_id = u.id
                               JOIN furniture fur ON o.furniture_id = fur.id
                               ORDER BY c.created_at DESC");
?>

<div class="container">
    <h3>All Complaints</h3>

    <?php if (!empty($msg)): ?>
    <div class="success-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="complaint-cards">
        <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card">
            <div class="card-image">
                <img src="uploads/<?php echo htmlspecialchars($row['furniture_image']); ?>" alt="Furniture" />
            </div>
            <div class="card-content">
                <h4><?php echo htmlspecialchars($row['furniture_name']); ?></h4>
                <p><strong>Buyer:</strong> <?php echo htmlspecialchars($row['buyer_name']); ?></p>
                <p><strong>Complaint:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                <p><strong>Status:</strong>
                    <?php echo !empty($row['status']) ? htmlspecialchars($row['status']) : "Pending"; ?></p>
                <?php if (!empty($row['reply'])): ?>
                <p><strong>Reply:</strong> <?php echo htmlspecialchars($row['reply']); ?></p>
                <p><small>Replied: <?php echo date("d M Y", strtotime($row['replied_at'])); ?></small></p>
                <?php endif; ?>
            </div>
            <div class="card-action">
                <form method="POST">
                    <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                    <textarea name="reply_text" placeholder="Write reply..."
                        required><?php echo htmlspecialchars($row['reply']); ?></textarea>
                    <select name="status" required>
                        <option value="">--Select Status--</option>
                        <option value="Resolved" <?php if ($row['status'] == 'Resolved') echo 'selected'; ?>>Resolved
                        </option>
                        <option value="Rejected" <?php if ($row['status'] == 'Rejected') echo 'selected'; ?>>Rejected
                        </option>
                        <option value="Pending"
                            <?php if ($row['status'] == 'Pending' || $row['status'] == '') echo 'selected'; ?>>Pending
                        </option>
                    </select>
                    <button type="submit" name="update_complaint">Update</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No complaints available.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: auto;
    padding: 1rem;
}

.complaint-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

.card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.card-image img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-content {
    padding: 1rem;
    flex: 1;
}

.card-content h4 {
    margin-top: 0;
    margin-bottom: 0.5rem;
}

.card-content p {
    margin: 0.25rem 0;
}

.card-action {
    padding: 1rem;
    border-top: 1px solid #eee;
}

.card-action form textarea {
    width: 100%;
    min-height: 60px;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.card-action form select {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.card-action form button {
    background: #5d3fd3;
    color: #fff;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.card-action form button:hover {
    background: #472bb5;
}
</style>