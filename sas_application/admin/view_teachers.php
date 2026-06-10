<?php
// Include database connection
include "header.php";
include '../other/db_connection.php';

$message = ""; // Initialize message variable
$messageClass = ""; // Initialize message CSS class

// Function to delete teacher
function deleteTeacher($teacher_id, $conn) {
    $delete_query = "DELETE FROM teachers WHERE teacher_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('i', $teacher_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

// Check if delete request is submitted via URL parameter
if (isset($_GET['delete_teacher'])) {
    $teacher_id = $_GET['teacher_id'];
    deleteTeacher($teacher_id, $conn);
    $message = "Teacher deleted successfully.";
    $messageClass = "success";
}

// Check if form is submitted for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_teacher'])) {
    // Retrieve form data
    $teacher_id = $_POST['teacher_id'];
    $teacher_name = $_POST['teacher_name'];
    $email = $_POST['email'];

    // Update teacher in the database
    $update_query = "UPDATE teachers SET teacher_name = ?, email = ? WHERE teacher_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssi', $teacher_name, $email, $teacher_id);
    
    if ($update_stmt->execute()) {
        $message = "Teacher updated successfully.";
        $messageClass = "success";
    } else {
        $message = "Failed to update teacher.";
        $messageClass = "error";
    }
    
    $update_stmt->close();
}

// Query to select all teachers
$query = "SELECT teacher_id, teacher_name, email FROM teachers WHERE status = 'approved'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Teachers</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <br>
    <br>
    <header>
        <h1>View Teachers</h1>
    </header>

    <?php if (!empty($message)) { ?>
        <p class="<?php echo $messageClass; ?>"><?php echo $message; ?></p>
    <?php } ?>

    <table>
        <tr>
            <th>Teacher ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        // Display teachers data
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['teacher_id'] . "</td>
                        <td>" . $row['teacher_name'] . "</td>
                        <td>" . $row['email'] . "</td>
                        <td>
                            <a href='view_teachers.php?edit_teacher=1&teacher_id=" . $row['teacher_id'] . "'>Edit</a> |
                            <a href='view_teachers.php?delete_teacher=1&teacher_id=" . $row['teacher_id'] . "' onclick=\"return confirm('Are you sure you want to delete this teacher?');\">Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No teachers found</td></tr>";
        }
        ?>
    </table>

    <?php
    // Check if edit form should be displayed
    if (isset($_GET['edit_teacher'])) {
        $teacher_id = $_GET['teacher_id'];

        // Fetch teacher details for editing
        $edit_query = "SELECT teacher_id, teacher_name, email FROM teachers WHERE teacher_id = ?";
        $edit_stmt = $conn->prepare($edit_query);
        $edit_stmt->bind_param('i', $teacher_id);
        $edit_stmt->execute();
        $edit_stmt->bind_result($teacher_id, $teacher_name, $email);
        $edit_stmt->fetch();
        $edit_stmt->close();
        ?>

        <h2>Edit Teacher</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
            <label for="teacher_name">Name:</label>
            <input type="text" id="teacher_name" name="teacher_name" value="<?php echo $teacher_name; ?>" required><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required><br>
            <button type="submit" name="edit_teacher">Update Teacher</button>
        </form>

    <?php } ?>

</body>
</html>
