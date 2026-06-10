<?php
include '../header/admin-header.php';
include '../connection.php';

/* Approve / Reject Action */
if (isset($_GET['id'], $_GET['status'])) {

    $leave_id = intval($_GET['id']);
    $status   = $_GET['status'] === 'Approved' ? 'Approved' : 'Rejected';
    $admin_id = $_SESSION['id'];

    mysqli_query($con, "
        UPDATE leaves
        SET status='$status', reviewed_by='$admin_id', reviewed_at=NOW()
        WHERE leave_id='$leave_id'
    ");

    /* If approved → set user availability = Leave */
    if ($status === 'Approved') {
        mysqli_query($con, "
            UPDATE user u
            JOIN leaves l ON u.id = l.user_id
            SET u.status='Leave'
            WHERE l.leave_id='$leave_id'
        ");
    }

    header("Location: verify_leave.php");
    exit;
}

/* Fetch Leave Requests */
$leaves = mysqli_query($con, "
    SELECT l.leave_id, u.name, u.employee_id,
           l.leave_from, l.leave_to, l.reason, l.status
    FROM leaves l
    JOIN user u ON l.user_id = u.id
    ORDER BY l.leave_date DESC
");
?>

<div class="container-fluid mt-5">
    <h3 class="text-center mb-4">Verify Leave Requests</h3>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Leave from</th>
                        <th>Leave To</th>
                        <th>Reason</th>
                        <th>Total Days</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($leaves) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($leaves)):

                            $from = new DateTime($row['leave_from']);
                            $to   = new DateTime($row['leave_to']);

                            // +1 because leave is inclusive (from → to)
                            $days = $from->diff($to)->days + 1;


                        ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['employee_id'] ?></td>
                        <td><?= $row['leave_from'] ?></td>
                        <td><?= $row['leave_to'] ?></td>
                        <td><?= $row['reason'] ?></td>
                        <td><strong><?= $days ?></strong></td>

                        <td>
                            <?php if ($row['status'] == 'Approved'): ?>
                            <span class="badge badge-success">Approved</span>
                            <?php elseif ($row['status'] == 'Rejected'): ?>
                            <span class="badge badge-danger">Rejected</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'Pending'): ?>
                            <a href="verify_leave.php?id=<?= $row['leave_id'] ?>&status=Approved"
                                class="btn btn-success btn-sm" onclick="return confirm('Approve this leave?')">
                                Approve
                            </a>
                            <a href="verify_leave.php?id=<?= $row['leave_id'] ?>&status=Rejected"
                                class="btn btn-danger btn-sm" onclick="return confirm('Reject this leave?')">
                                Reject
                            </a>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>
                                Action Taken
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No leave requests found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>