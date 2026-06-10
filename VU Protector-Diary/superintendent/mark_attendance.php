<?php
include '../header/superintendent-header.php';
include '../connection.php';

$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

if ($role !== 'Superintendent') {
    die("Access denied");
}

// Fetch duties and check if staff has marked
$duties = mysqli_query($con, "
    SELECT a.assignment_id, at.status, e.exam_name, e.exam_date, e.center,
           at.id AS attendance_id
    FROM assignments a
    JOIN exams e ON a.exam_id = e.exam_id
    LEFT JOIN attendance at ON at.assignment_id = a.assignment_id AND at.marked_by='$user_id'
    WHERE a.user_id='$user_id'
    ORDER BY e.exam_date ASC
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignment_id = $_POST['assignment_id'];

    $check = mysqli_query($con, "SELECT id FROM attendance WHERE assignment_id='$assignment_id' AND marked_by='$user_id'");

    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Attendance already marked'); window.history.back();</script>";
        exit;
    } else {
        mysqli_query($con, "
            INSERT INTO attendance (assignment_id, marked_by)
            VALUES ('$assignment_id','$user_id')
        ");
        echo "<script>alert('Attendance marked successfully'); window.history.back();</script>";
    }
}
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">My Exam Duties</h3>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Exam</th>
                        <th>Date</th>
                        <th>Center</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($duties)) {
                        $marked = $row['status'] == 'Marked';
                        $absent = $row['status'] == 'Absent';
                        $present = $row['status'] == 'Present';

                    ?>
                    <tr>
                        <td><?= $row['exam_name'] ?></td>
                        <td><?= $row['exam_date'] ?></td>
                        <td><?= $row['center'] ?></td>
                        <td>
                            <?php if ($marked): ?>
                            <span class="badge badge-secondary">Marked</span>
                            <?php elseif ($absent): ?>
                            <span class="badge badge-danger">Absent</span>
                            <?php elseif ($present): ?>
                            <span class="badge badge-success">Present</span>
                            <?php else: ?>

                            <span class="badge badge-warning">Not Marked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] != 'Marked' && $row['status'] != 'Present'): ?>
                            <form method="post" action="">
                                <input type="hidden" name="assignment_id" value="<?= $row['assignment_id'] ?>">
                                <button class="btn btn-success btn-sm">Mark Attendance</button>
                            </form>

                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>Already Marked</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>