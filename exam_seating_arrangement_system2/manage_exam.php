<?php
include 'db.php';
include 'header.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Add exam schedule
if (isset($_POST['add_exam'])) {
    $course_id = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];

    $stmt = $conn->prepare("INSERT INTO exam_schedules (course_id, exam_date, start_time, end_time, room) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $course_id, $exam_date, $start_time, $end_time, $room);
    $stmt->execute();
}

// Delete exam
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM exam_schedules WHERE id = " . intval($_GET['delete']));
}

// Fetch all exams
$exams = $conn->query("SELECT e.*, c.course_name 
                       FROM exam_schedules e 
                       JOIN courses c ON e.course_id = c.course_id");

$courses = $conn->query("SELECT * FROM courses");
?>

<div class="container mt-4">
    <h2>Manage Exam Schedules</h2>

    <form method="post" class="row g-3 mt-3">
        <div class="col-md-3">
            <select name="course_id" class="form-control" required>
                <option value="">Select Course</option>
                <?php while ($c = $courses->fetch_assoc()) { ?>
                <option value="<?= $c['course_id'] ?>"><?= $c['course_name'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="exam_date" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="time" name="start_time" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="time" name="end_time" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="room" class="form-control" placeholder="Room Name" required>
        </div>
        <div class="col-md-1">
            <button type="submit" name="add_exam" class="btn btn-primary w-100">Add</button>
        </div>
    </form>

    <table class="table table-striped mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Room</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($exam = $exams->fetch_assoc()) { ?>
            <tr>
                <td><?= $exam['id'] ?></td>
                <td> <?= $exam['course_name'] ?></td>
                <td><?= $exam['exam_date'] ?></td>
                <td><?= $exam['start_time'] ?></td>
                <td><?= $exam['end_time'] ?></td>
                <td><?= htmlspecialchars($exam['room']) ?></td>
                <td>
                    <a href="?delete=<?= $exam['id'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this exam schedule?');">
                        Delete
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>