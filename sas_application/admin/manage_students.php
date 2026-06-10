<?php
ob_start();
include 'header.php';
include '../other/db_connection.php';

// Delete action
if (isset($_GET['delete'])) {
    $student_id = intval($_GET['delete']);
    $query = "DELETE FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    header("Location: manage_students.php");
    exit();
}

// Edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $student_id = intval($_POST['student_id']);
    $student_name = htmlspecialchars($_POST['student_name']);
    $gender = htmlspecialchars($_POST['gender']);
    $dob = htmlspecialchars($_POST['dob']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $class_id = htmlspecialchars($_POST['class_id']);

    // Update student data in the database
    $query = "UPDATE students SET student_name = ?, gender = ?, dob = ?, email = ?, class_id = ? WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssi', $student_name, $gender, $dob, $email, $class_id, $student_id);
    $stmt->execute();
    header("Location: manage_students.php");
    exit();
}

// Fetch all student data
$query = "SELECT student_id, student_name, gender, dob, email, class_id FROM students";
$result = $conn->query($query);

// Fetch student data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $student_id = intval($_GET['edit']);
    $query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
<div style="margin-top: 48px;">
    <div style="background-color: #0f582d; padding:5px; text-align:center; color:white;">

   
<h3 >
    Student Information
</h3>
</div>
<table border="1">
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Email</th>
        <th>Class ID</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['student_id'] ?></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['dob']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['class_id']) ?></td>
            <td>
                <a href="manage_students.php?edit=<?= $row['student_id'] ?>">Edit</a> |
                <a href="manage_students.php?delete=<?= $row['student_id'] ?>">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</div>
<?php if ($edit_data): ?>
   
    <div style="background-color: #0f582d; padding:5px; text-align:center; color:white;">

   
<h3 >
    Update Information
</h3>
</div>
    <form method="POST" action="manage_students.php">
        <input type="hidden" name="student_id" value="<?= $edit_data['student_id'] ?>">
        <p>Name: <input type="text" name="student_name" value="<?= htmlspecialchars($edit_data['student_name']) ?>"></p>
        <p>Gender: <input type="text" name="gender" value="<?= htmlspecialchars($edit_data['gender']) ?>"></p>
        <p>Date of Birth: <input type="date" name="dob" value="<?= htmlspecialchars($edit_data['dob']) ?>"></p>
        <p>Email: <input type="email" name="email" value="<?= htmlspecialchars($edit_data['email']) ?>"></p>
        <p>Class ID: <input type="text" name="class_id" value="<?= htmlspecialchars($edit_data['class_id']) ?>"></p>
        <p><input type="submit" name="edit" value="Update"></p>
    </form>
<?php endif; ?>

</body>
</html>
