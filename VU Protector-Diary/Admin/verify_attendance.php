<?php
include '../header/admin-header.php';
include '../connection.php';






if (isset($_GET['id'])) {


    $admin_id = $_SESSION['id'];
    $attendance_id = intval($_GET['id']);
    $status = $_GET['status'] === 'Present' ? 'Present' : 'Absent';
    $admin_id = $_SESSION['id'];

    mysqli_query($con, "
    UPDATE attendance
    SET status='$status', verified_by='$admin_id', verified_at=NOW()
    WHERE id='$attendance_id'
");

    header("Location: verify_attendance.php");
    exit;
}

$records = mysqli_query($con, "
    SELECT at.id AS attendance_id, u.name AS staff_name, e.exam_name, e.exam_date, e.center,
           at.marked_at, at.status
    FROM attendance at
    JOIN assignments a ON at.assignment_id = a.assignment_id
    JOIN user u ON a.user_id = u.id
    JOIN exams e ON a.exam_id = e.exam_id
    ORDER BY e.exam_date ASC
");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Verify Attendance</h3>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Staff</th>
                        <th>Exam</th>
                        <th>Date</th>
                        <th>Center</th>
                        <th>Marked At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($records)): ?>
                        <tr>
                            <td><?= $row['staff_name'] ?></td>
                            <td><?= $row['exam_name'] ?></td>
                            <td><?= $row['exam_date'] ?></td>
                            <td><?= $row['center'] ?></td>
                            <td><?= $row['marked_at'] ?></td>
                            <td>
                                <?php
                                if ($row['status'] == 'Present') echo '<span class="badge badge-success">Present</span>';
                                elseif ($row['status'] == 'Absent') echo '<span class="badge badge-danger">Absent</span>';
                                else echo '<span class="badge badge-warning">Pending</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'Marked'): ?>
                                    <a href="verify_attendance.php?id=<?= $row['attendance_id'] ?>&status=Present"
                                        class="btn btn-success btn-sm">Present</a>
                                    <a href="verify_attendance.php?id=<?= $row['attendance_id'] ?>&status=Absent"
                                        class="btn btn-danger btn-sm">Absent</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Verified</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>