<?php
// Include database connection
include "header.php";
include '../other/db_connection.php';

$message = ""; // Initialize message variable
$messageClass = ""; // Initialize message CSS class

// Function to delete parent
function deleteParent($parent_id, $conn) {
    $delete_query = "DELETE FROM parents WHERE parent_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('i', $parent_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

// Check if delete request is submitted via URL parameter
if (isset($_GET['delete_parent']) && isset($_GET['parent_id'])) {
    $parent_id = $_GET['parent_id'];
    deleteParent($parent_id, $conn);
    $message = "Parent deleted successfully.";
    $messageClass = "success";
}

// Check if form is submitted for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_parent'])) {
    // Retrieve form data
    $parent_id = $_POST['parent_id'];
    $parent_name = $_POST['parent_name'];
    $phone = $_POST['phone'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update parent in the database
    $update_query = "UPDATE parents SET parent_name = ?, phone = ?, student_id = ?, email = ? WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssssi', $parent_name, $phone, $student_id, $email, $parent_id);

    if ($update_stmt->execute()) {
        $message = "Parent updated successfully.";
        $messageClass = "success";
    } else {
        $message = "Failed to update parent.";
        $messageClass = "error";
    }

    $update_stmt->close();
}

// Query to select all approved parents
$query = "SELECT parent_id, parent_name, student_id, email, status FROM parents WHERE status = 'approved'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Parents</title>
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/table.css">
</head>

<body>
    <br><br>
    <header>
        <h2>View Parents</h2>
    </header>

    <div class="container">
        <?php if (!empty($message)) { ?>
            <p class="message <?php echo $messageClass; ?>"><?php echo $message; ?></p>
        <?php } ?>

        <table>
            <tr>
                <th>Parent ID</th>
                <th>Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php
            // Display parents data
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['parent_id']) . "</td>
                            <td>" . htmlspecialchars($row['parent_name']) . "</td>
                            <td>" . htmlspecialchars($row['student_id']) . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>
                                <a href='view_parents.php?edit_parent=1&parent_id=" . urlencode($row['parent_id']) . "'>Edit</a> |
                                <a href='view_parents.php?delete_parent=1&parent_id=" . urlencode($row['parent_id']) . "' onclick=\"return confirm('Are you sure you want to delete this parent?');\">Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No parents found</td></tr>";
            }
            ?>
        </table>

        <?php
        // Check if edit form should be displayed
        if (isset($_GET['edit_parent']) && isset($_GET['parent_id'])) {
            $parent_id = $_GET['parent_id'];

            // Fetch parent details for editing
            $edit_query = "SELECT parent_id, parent_name, phone, student_id, email, password, status FROM parents WHERE parent_id = ?";
            $edit_stmt = $conn->prepare($edit_query);
            $edit_stmt->bind_param('i', $parent_id);
            $edit_stmt->execute();
            $edit_stmt->bind_result($parent_id, $parent_name, $phone, $student_id, $email, $password, $status);
            $edit_stmt->fetch();
            $edit_stmt->close();
            ?>

            <h2>Edit Parent</h2>
            <form method="POST" action="view_parents.php">
                <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($parent_id); ?>">
                <label for="parent_name">Name:</label>
                <input type="text" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($parent_name); ?>" required>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                <label for="student_id">Child's ID:</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                
                <button type="submit" name="edit_parent">Update Parent</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>
