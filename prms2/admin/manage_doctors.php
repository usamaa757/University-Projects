<?php
include 'sidebar.php';
require_once '../db.php'; // Make sure this connects to your database

// Approve or reject logic
if (isset($_GET['action'], $_GET['id'])) {
    $doctorId = (int)$_GET['id'];
    $action = $_GET['action'] === 'approve' ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE doctors SET status = ? WHERE doctor_id = ?");
    $stmt->bind_param("si", $action, $doctorId);
    if ($stmt->execute()) {
        echo "<script>alert('Statu updated successfully!'); window.location.href='manage_doctors.php';</script>";
    } else {
        echo "<script>alert('Error updated status.'); window.history.back();</script>";
        exit;
    }

    $stmt->close();
}

// Fetch doctors
$result = $conn->query("SELECT * FROM doctors WHERE status = 'pending' ORDER BY doctor_id DESC");
?>

<!-- Main Content -->
<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Manage Doctors</h2>

        <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Specialty</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1;
                        while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['specialization']) ?></td>
                        <td>
                            <?php if ($row['status'] == 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                            <?php elseif ($row['status'] == 'rejected'): ?>
                            <span class="badge bg-danger">Rejected</span>
                            <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                            <a href="?action=approve&id=<?= $row['doctor_id'] ?>"
                                class="btn btn-sm btn-success">Approve</a>
                            <a href="?action=reject&id=<?= $row['doctor_id'] ?>"
                                class="btn btn-sm btn-danger">Reject</a>
                            <?php else: ?>
                            <em>N/A</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">No doctors found.</div>
        <?php endif; ?>
    </div>
</div>

</body>

</html>