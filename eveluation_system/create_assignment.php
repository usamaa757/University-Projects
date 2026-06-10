<?php
include 'navbar.php';
require 'db.php';

// Check if faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

$faculty_id = $_SESSION['user_id'];
$msg = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = intval($_POST['category']);
    $due_date = $_POST['due_date'];

    if (empty($title) || empty($due_date)) {
        $error = 'Title and Due Date are required.';
    } else {
        $sql = "INSERT INTO assignments (faculty_id, title, description, category, due_date) VALUES ($faculty_id, '$title', '$description', $category, '$due_date')";
        if (mysqli_query($conn, $sql)) {
            $msg = 'Assignment created successfully.';
        } else {
            $error = 'Database error: ' . mysqli_error($conn);
        }
    }
}
?>



<div class="container">
    <h2>Create New Assignment</h2>
    <?php if (!empty($msg)) echo '<p class="msg">' . $msg . '</p>'; ?>
    <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>
    <form action="" method="POST">
        <label for="title">Assignment Title</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="category">Category</label>
        <select name="category" id="category" required>
            <option value="">-- Select Category --</option>
            <option value="Computer Science<">Computer Science</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Electrical Engineering">Electrical Engineering</option>
            <option value="Mechanical Engineering">Mechanical Engineering</option>
            <option value="Civil Engineering">Civil Engineering</option>
            <option value="Business Administration">Business Administration</option>
            <option value="Biotechnology">Biotechnology</option>
        </select>

        <label for="due_date">Due Date</label>
        <input type="datetime-local" name="due_date" id="due_date" required>

        <button type="submit">Create Assignment</button>
    </form>
</div>

</body>

</html>