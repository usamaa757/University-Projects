<?php
include 'header.php';
include '../db.php';
$success = "";
$error = "";
// Handle deletion if requested
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    // Step 1: Get all property IDs belonging to the agent
    $propStmt = $conn->prepare("SELECT id FROM properties WHERE agent_id = ?");
    $propStmt->bind_param("i", $deleteId);
    $propStmt->execute();
    $propResult = $propStmt->get_result();

    while ($prop = $propResult->fetch_assoc()) {
        $property_id = $prop['id'];

        // Delete images from disk
        $imgStmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
        $imgStmt->bind_param("i", $property_id);
        $imgStmt->execute();
        $imgResult = $imgStmt->get_result();
        while ($img = $imgResult->fetch_assoc()) {
            if (file_exists($img['image_path'])) {
                unlink($img['image_path']);
            }
        }

        // Delete property images
        $delImgStmt = $conn->prepare("DELETE FROM property_images WHERE property_id = ?");
        if ($delImgStmt) {
            $delImgStmt->bind_param("i", $property_id);
            $delImgStmt->execute();
            $delImgStmt->close();
        }

        // Delete property features
        $delFeatStmt = $conn->prepare("DELETE FROM property_features WHERE property_id = ?");
        if ($delFeatStmt) {
            $delFeatStmt->bind_param("i", $property_id);
            $delFeatStmt->execute();
            $delFeatStmt->close();
        }
    }

    // Delete properties
    $delPropStmt = $conn->prepare("DELETE FROM properties WHERE agent_id = ?");
    if ($delPropStmt) {
        $delPropStmt->bind_param("i", $deleteId);
        $delPropStmt->execute();
        $delPropStmt->close();
    }

    // Delete the agent
    $delAgentStmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'agent'");
    if ($delAgentStmt) {
        $delAgentStmt->bind_param("i", $deleteId);
        $delAgentStmt->execute();
        $delAgentStmt->close();
    }

    $success = "Agent and all related properties deleted successfully.";
}

// Fetch all agents
$result = $conn->query("SELECT id, fullname, email, phone FROM users WHERE role = 'agent'");
?>



<div class="section-header">

    <h2>Manage Agents</h2>
</div>


<?php if ($result->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <?php $i = 1;
            while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td class="action-buttons">
                <a class="edit-btn" href="edit_agent.php?id=<?= $row['id'] ?>" class="btn small"> Edit</a>
                <a class="delete-btn" href="manage_agents.php?delete=<?= $row['id'] ?>" class="btn small danger"
                    onclick="return confirm('Are you sure you want to delete this agent?')"> Delete</a>
                <a class="edit-btn" href="feedback.php?agent_id=<?= $row['id'] ?>" class="btn small"> Feedback</a>

            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p>No agents found.</p>
<?php endif; ?><?php include '../footer.php'; ?>