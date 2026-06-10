<?php
include 'db.php';
include 'header.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Handle course assignment
if (isset($_POST['assign'])) {
    $student_ids = $_POST['student_id'];
    $course_ids = $_POST['course_id'];

    for ($i = 0; $i < count($student_ids); $i++) {
        $student_id = $student_ids[$i];
        $course_id = $course_ids[$i];

        if ($course_id != '') {
            $stmt = $conn->prepare("UPDATE students SET course_id = ? WHERE student_id = ?");
            $stmt->bind_param("ii", $course_id, $student_id);
            $stmt->execute();
        }
    }

    echo "<div class='alert alert-success mt-3'>Courses assigned successfully!</div>";
}

// Handle reset
if (isset($_POST['reset'])) {
    $conn->query("UPDATE students SET course_id = NULL");
    $conn->query("TRUNCATE TABLE seating_arrangements");
    echo "<div class='alert alert-warning mt-3'>All course assignments and seating arrangements have been reset.</div>";
}

// Fetch courses
$courses = $conn->query("SELECT * FROM courses");
$course_list = [];
while ($c = $courses->fetch_assoc()) {
    $course_list[] = $c;
}

// Fetch unassigned students
$unassigned_students = $conn->query("
    SELECT student_id, student_name, email 
    FROM students 
    WHERE course_id IS NULL
");

// Fetch assigned students
$assigned_students = $conn->query("
    SELECT s.student_id, s.student_name, s.email, c.course_name 
    FROM students s
    JOIN courses c ON s.course_id = c.course_id
");
?>

<div class="container mt-4">
    <h2>Assign Course to Students</h2>

    <!-- Unassigned Students -->
    <h4 class="mt-4">Unassigned Students</h4>
    <?php if ($unassigned_students->num_rows > 0): ?>
    <form method="post">
        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assign Course</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = $unassigned_students->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?= $s['student_id'] ?>
                        <input type="hidden" name="student_id[]" value="<?= $s['student_id'] ?>">
                    </td>
                    <td><?= htmlspecialchars($s['student_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td>
                        <select name="course_id[]" class="form-control" required>
                            <option value="">-- Select Course --</option>
                            <?php foreach ($course_list as $course) { ?>
                            <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_name']) ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" name="assign" class="btn btn-primary">Assign Selected</button>
    </form>
    <?php else: ?>
    <p class="text-muted">All students have already been assigned a course.</p>
    <?php endif; ?>

    <!-- Assigned Students -->
    <h4 class="mt-5">Already Assigned Students</h4>
    <?php if ($assigned_students->num_rows > 0): ?>
    <table class="table table-bordered mt-3">
        <thead class="table-secondary">
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Assigned Course</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = $assigned_students->fetch_assoc()) { ?>
            <tr>
                <td><?= $s['student_id'] ?></td>
                <td><?= htmlspecialchars($s['student_name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['course_name']) ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="text-muted">No students have been assigned a course yet.</p>
    <?php endif; ?>

    <!-- Reset Button -->
    <form method="post" class="mt-4">
        <button type="submit" name="reset" class="btn btn-danger"
            onclick="return confirm('Are you sure you want to reset all course assignments and seating arrangements?');">
            Reset All Assignments & Seating
        </button>
    </form>
</div>