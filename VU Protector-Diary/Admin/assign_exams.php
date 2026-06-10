<?php
include '../connection.php';
include '../mail_config.php';

// Fetch exams
$exam_res = mysqli_query($con, "SELECT * FROM exams ORDER BY exam_date ASC");

// Fetch available users
$users_res = mysqli_query($con, "SELECT * FROM user WHERE role IN ('Superintendent','Invigilator') AND status='Available'");

// Handle assignment submission
if (isset($_POST['assign'])) {
    $exam_id = $_POST['exam_id'];
    $user_id = $_POST['user_id'];

    // Check for scheduling conflicts
    $conflict_check = mysqli_query($con, "
        SELECT a.* FROM assignments a
        JOIN exams e ON a.exam_id=e.exam_id
        WHERE a.user_id='$user_id'
        AND e.exam_date=(SELECT exam_date FROM exams WHERE exam_id='$exam_id')
        AND ((e.start_time <= (SELECT end_time FROM exams WHERE exam_id='$exam_id') 
        AND e.end_time >= (SELECT start_time FROM exams WHERE exam_id='$exam_id')))
    ");

    if (mysqli_num_rows($conflict_check) > 0) {
        echo "<script>alert('Scheduling conflict detected for this user!');</script>";
    } else {

        $insert = mysqli_query($con, "
        INSERT INTO assignments (exam_id, user_id) 
        VALUES ('$exam_id', '$user_id')
    ");

        if ($insert) {

            // Fetch exam details
            $examQ = mysqli_query($con, "
            SELECT exam_name, exam_date, start_time, end_time, center 
            FROM exams 
            WHERE exam_id='$exam_id'
        ");
            $exam = mysqli_fetch_assoc($examQ);

            // Fetch user details
            $userQ = mysqli_query($con, "
            SELECT name, email, role 
            FROM user 
            WHERE id='$user_id'
        ");
            $user = mysqli_fetch_assoc($userQ);

            // Prepare email
            $subject = "Exam Duty Assignment Notification";

            $message = "
            <h3>Duty Assignment Notice</h3>
            <p>Dear <b>{$user['name']}</b>,</p>

            <p>You have been assigned as <b>{$user['role']}</b> for the following exam:</p>

            <ul>
                <li><b>Exam:</b> {$exam['exam_name']}</li>
                <li><b>Date:</b> {$exam['exam_date']}</li>
                <li><b>Time:</b> {$exam['start_time']} - {$exam['end_time']}</li>
                <li><b>Center:</b> {$exam['center']}</li>
            </ul>

            <p>Please report at least 30 minutes before the exam start time.</p>
            <br>
            <p><b>Examination Duty Management System</b></p>
        ";

            // Send email
            sendDutyMail(
                $user['email'],
                $user['name'],
                $subject,
                $message
            );

            echo "<script>alert('User assigned successfully & email sent');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Assign Proctors</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <?php include '../header/admin-header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Assign Proctors to Exams</h2>

        <div class="card shadow-sm p-4 mb-5">
            <form method="POST">
                <div class="form-group">
                    <label>Exam</label>
                    <select name="exam_id" class="form-control" required>
                        <option value="">Select Exam</option>
                        <?php while ($exam = mysqli_fetch_assoc($exam_res)) { ?>
                        <option value="<?= $exam['exam_id'] ?>">
                            <?= $exam['exam_name'] ?> | <?= $exam['exam_date'] ?> | <?= $exam['center'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>User</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Select User</option>
                        <?php
                        mysqli_data_seek($users_res, 0);
                        while ($user = mysqli_fetch_assoc($users_res)) { ?>
                        <option value="<?= $user['id'] ?>">
                            <?= $user['name'] ?> (<?= $user['role'] ?>)
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" name="assign" class="btn btn-success ">Assign</button>
            </form>
        </div>

        <h3 class="mb-3">Existing Assignments</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Exam</th>
                        <th>User</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $assignments_res = mysqli_query($con, "
                    SELECT a.*, u.name as username, u.role, e.exam_name
                    FROM assignments a
                    JOIN user u ON a.user_id=u.id
                    JOIN exams e ON a.exam_id=e.exam_id
                    ORDER BY e.exam_date ASC
                ");
                    while ($assign = mysqli_fetch_assoc($assignments_res)) { ?>
                    <tr>
                        <td><?= $assign['exam_name'] ?></td>
                        <td><?= $assign['username'] ?></td>
                        <td><?= $assign['role'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>