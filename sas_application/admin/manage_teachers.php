<?php
ob_start();
include 'header.php';
include '../other/db_connection.php';

// Delete action
if (isset($_GET['delete'])) {
    $teacher_id = intval($_GET['delete']);
    $query = "DELETE FROM teachers WHERE teacher_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $teacher_id);
    $stmt->execute();
    header("Location: manage_teachers.php");
    exit();
}

// Edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $teacher_id = intval($_POST['teacher_id']);
    $teacher_name = htmlspecialchars($_POST['teacher_name']);
    $gender = htmlspecialchars($_POST['gender']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
   
    // Update teacher data in the database
    $query = "UPDATE teachers SET teacher_name = ?, email = ?  WHERE teacher_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $teacher_name, $email, $teacher_id);
    $stmt->execute();
    header("Location: manage_teachers.php");
    exit();
}

// Fetch all teacher data
$query = "SELECT teacher_id, teacher_name, email FROM teachers";
$result = $conn->query($query);

// Fetch teacher data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $teacher_id = intval($_GET['edit']);
    $query = "SELECT * FROM teachers WHERE teacher_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $teacher_id);
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
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
<div style="margin-top: 48px;">
    <div style="background-color: #0f582d; padding:5px; text-align:center; color:white;">

   
<h3 >
    Teacher Information
</h3>
</div>
<table border="1">
    <tr>
        <th>Teacher ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['teacher_id'] ?></td>
            <td><?= htmlspecialchars($row['teacher_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <a href="manage_teachers.php?edit=<?= $row['teacher_id'] ?>">Edit</a> |
                <a href="manage_teachers.php?delete=<?= $row['teacher_id'] ?>">Delete</a>
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
    <form method="POST" action="manage_teachers.php">
        <input type="hidden" name="teacher_id" value="<?= $edit_data['teacher_id'] ?>">
        <p>Name: <input type="text" name="teacher_name" value="<?= htmlspecialchars($edit_data['teacher_name']) ?>"></p>
       <p>Email: <input type="email" name="email" value="<?= htmlspecialchars($edit_data['email']) ?>"></p>
       <p><input type="submit" name="edit" value="Update"></p>
    </form>
<?php endif; ?>

</body>
</html>
