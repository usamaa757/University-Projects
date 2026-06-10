<?php
include 'header.php';
include '../config/database.php';

// Handle Accept/Reject actions
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = (int) $_GET['id'];
    if (in_array($action, ['accept', 'reject'])) {
        $newStatus = $action === 'accept' ? 'accepted' : 'rejected';
        $stmt = $conn->prepare("UPDATE doctors SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_doctor.php");
        exit;
    }
}

// Fetch pending doctors
$pending_doctors = $conn->query("SELECT * FROM doctors WHERE status = 'pending' ORDER BY name");

// Fetch accepted doctors
$accepted_doctors = $conn->query("SELECT * FROM doctors WHERE status = 'accepted' ORDER BY name");
?>

<div class="container mt-4">
    <h4>🕐 Pending Doctor Registrations</h4>
    <?php if ($pending_doctors->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead class="table-warning">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($doc = $pending_doctors->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($doc['name']) ?></td>
                <td><?= htmlspecialchars($doc['email']) ?></td>
                <td><?= htmlspecialchars($doc['department'] ?? '-') ?></td>
                <td>
                    <a href="?action=accept&id=<?= $doc['id'] ?>" class="btn btn-success btn-sm">✅ Accept</a>
                    <a href="?action=reject&id=<?= $doc['id'] ?>" class="btn btn-danger btn-sm">❌ Reject</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-info">No pending doctor registrations.</div>
    <?php endif; ?>

    <hr class="my-5">

    <h4>✅ Accepted Doctors</h4>
    <?php if ($accepted_doctors->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($doc = $accepted_doctors->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($doc['name']) ?></td>
                <td><?= htmlspecialchars($doc['email']) ?></td>
                <td><?= htmlspecialchars($doc['department'] ?? '-') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-info">No accepted doctors yet.</div>
    <?php endif; ?>
</div>