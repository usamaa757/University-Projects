<?php
include 'header.php';
include '../db.php';

// Insert new guidance
if (isset($_POST['add_guidance'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO guidance (title, type, content) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $type, $content);
    $stmt->execute();
}

// Delete guidance
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM guidance WHERE guidance_id = $id");
}

// Get all guidance
$result = $conn->query("SELECT * FROM guidance ORDER BY created_at DESC");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Add New Guidance</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="mb-5">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="text">Text</option>
                                <option value="video">Video (YouTube/Vimeo URL)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="4"
                                placeholder="Write content or paste video URL..." required></textarea>
                        </div>
                        <div class="text-center">

                            <button type="submit" name="add_guidance" class="btn btn-dark">Add Guidance</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
    <h3 class="mb-3">Existing Guidance</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Content Preview</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['guidance_id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= ucfirst($row['type']) ?></td>
                <td>
                    <?php if ($row['type'] === 'text'): ?>
                    <?= substr(htmlspecialchars($row['content']), 0, 50) ?>...
                    <?php else: ?>
                    <a href="<?= htmlspecialchars($row['content']) ?>" target="_blank">Watch Video</a>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?delete=<?= $row['guidance_id'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this guidance?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No guidance content added yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>

</html>