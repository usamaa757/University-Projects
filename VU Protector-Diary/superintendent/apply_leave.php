<?php
include '../header/superintendent-header.php';
include '../connection.php';

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $from   = $_POST['leave_from'];
    $to     = $_POST['leave_to'];
    $reason = $_POST['reason'];

    mysqli_query($con, "
        INSERT INTO leaves (user_id, leave_from, leave_to, reason)
        VALUES ('$user_id', '$from', '$to', '$reason')
    ");

    echo "<script>alert('Leave applied successfully');</script>";
}

/* Fetch user leaves */
$leaves = mysqli_query($con, "
    SELECT leave_from, leave_to, reason, status, applied_at
    FROM leaves
    WHERE user_id='$user_id'
    ORDER BY leave_from DESC
");
?>

<div class="container mt-5">

    <!-- Apply Leave -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4>Apply for Leave</h4>
            <form method="post">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>From Date</label>
                        <input type="date" name="leave_from" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>To Date</label>
                        <input type="date" name="leave_to" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" required></textarea>
                </div>

                <button class="btn btn-success">Apply Leave</button>
            </form>
        </div>
    </div>

    <!-- Leave Records -->
    <div class="card shadow">
        <div class="card-body">
            <h4>My Leave Records</h4>

            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th>Total Days</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($leaves)):

                        $from = new DateTime($row['leave_from']);
                        $to   = new DateTime($row['leave_to']);

                        $days = $from->diff($to)->days + 1;
                    ?>
                    <tr>
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
                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>