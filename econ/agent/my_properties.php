<?php
include 'header.php';
include '../db.php';


$agent_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle deletion if `delete` is set in the URL
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $property_id = (int)$_GET['delete'];

    // Check ownership
    $stmt = $conn->prepare("SELECT id FROM properties WHERE id = ? AND agent_id = ?");
    $stmt->bind_param("ii", $property_id, $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete images
        $img_stmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
        $img_stmt->bind_param("i", $property_id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        while ($row = $img_result->fetch_assoc()) {
            if (file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }
        }

        $stmt1 = $conn->prepare("DELETE FROM property_images WHERE property_id = ?");
        if ($stmt1) {
            $stmt1->bind_param("i", $property_id);
            $stmt1->execute();
            $stmt1->close();
        } else {
            die("Error preparing statement 1: " . $conn->error);
        }

        $stmt2 = $conn->prepare("DELETE FROM property_features WHERE property_id = ?");
        if ($stmt2) {
            $stmt2->bind_param("i", $property_id);
            $stmt2->execute();
            $stmt2->close();
        } else {
            die("Error preparing statement 2: " . $conn->error);
        }

        $del_stmt = $conn->prepare("DELETE FROM properties WHERE id = ? AND agent_id = ?");
        $del_stmt->bind_param("ii", $property_id, $agent_id);
        if ($del_stmt->execute()) {
            $success = " Property deleted successfully.";
        } else {
            $erro = " Failed to delete property.";
        }
    } else {
        $error = " Property not found or access denied.";
    }
}


// Fetch agent properties
$stmt = $conn->prepare("SELECT * FROM properties WHERE agent_id = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="section-header">

    <h2>My Listed Properties</h2>
</div>

<?php if ($success): ?>
<div class="alert success"><?= $success ?></div>
<?php elseif ($error): ?>
<div class="alert error"><?= $error ?></div>
<?php endif; ?>

<?php if ($result->num_rows > 0): ?>
<table>
    <tr>
        <th>Title</th>
        <th>City</th>
        <th>Price</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['city']) ?></td>
        <td>$ <?= number_format($row['price']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td class="action-buttons">
            <a class="edit-btn" href="edit_property.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="delete-btn" href="my_properties.php?delete=<?= $row['id'] ?>"
                onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>You have not listed any properties yet.</p>
<?php endif; ?>

<?php include '../footer.php'; ?>

</body>

</html>