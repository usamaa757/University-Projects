<?php
include 'db.php';
include 'header.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Add course
if (isset($_POST['add_course'])) {

    $name = $_POST['course_name'];
    $stmt = $conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

// Delete course
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];
    $conn->query("DELETE FROM courses WHERE course_id = $course_id");
}

// Fetch all courses
$courses = $conn->query("SELECT * FROM courses");
?>

<div class="container mt-4">
    <h2>Manage Courses</h2>
    <form method="post" class="row g-3 mt-3">

        <div class="col-md-6">
            <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_course" class="btn btn-primary w-100">Add Course</button>
        </div>
    </form>

    <table class="table table-bordered table-striped mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $courses->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['course_id']) ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td>
                    <a href="?delete=<?= $row['course_id'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this course?');">
                        Delete
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>