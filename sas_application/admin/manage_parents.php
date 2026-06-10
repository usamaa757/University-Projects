<?php
// Include database connection
include '../other/db_connection.php';

// Message variable
$message = '';

// Check what action is being requested (Add, Update, Delete)
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'Add':
            $parent_name = $_POST['parent_name'];
            $email = $_POST['email'];
            $child_id = $_POST['child_id'];
            // Add parent
            $sql = "INSERT INTO parents (parent_name, email, child_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $parent_name, $email, $child_id);
            if ($stmt->execute()) {
                $message = 'Parent added successfully!';
            } else {
                $message = 'Error adding parent!';
            }
            break;
        
        case 'Update':
            if(isset($_POST['parent_id'])) {
                $parent_id = $_POST['parent_id'];
                $parent_name = $_POST['parent_name'];
                $email = $_POST['email'];
                $child_id = $_POST['child_id'];
                // Update parent
                $sql = "UPDATE parents SET parent_name = ?, email = ?, child_id = ? WHERE parent_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssii", $parent_name, $email, $child_id, $parent_id);
                if ($stmt->execute()) {
                    $message = 'Parent updated successfully!';
                } else {
                    $message = 'Error updating parent!';
                }
            } else {
                $message = 'Error: Parent ID not provided for updating!';
            }
            break;

        case 'Delete':
            if(isset($_POST['parent_id'])) {
                $parent_id = $_POST['parent_id'];
                // Now delete the parent
                $sql_delete_parent = "DELETE FROM parents WHERE parent_id = ?";
                $stmt_delete_parent = $conn->prepare($sql_delete_parent);
                $stmt_delete_parent->bind_param("i", $parent_id);
                if ($stmt_delete_parent->execute()) {
                    $message = 'Parent deleted successfully!';
                } else {
                    $message = 'Error deleting parent!';
                }
            } else {
                $message = 'Error: Parent ID not provided for deletion!';
            }
            break;
        
        case 'Edit':
            if(isset($_POST['parent_id'])) {
                $parent_id = $_POST['parent_id'];
                // Fetch parent details from the database
                $sql_fetch_parent = "SELECT * FROM parents WHERE parent_id = ?";
                $stmt_fetch_parent = $conn->prepare($sql_fetch_parent);
                $stmt_fetch_parent->bind_param("i", $parent_id);
                if ($stmt_fetch_parent->execute()) {
                    $result_fetch_parent = $stmt_fetch_parent->get_result();
                    if ($result_fetch_parent->num_rows > 0) {
                        $row = $result_fetch_parent->fetch_assoc();
                        $parent_name = $row['parent_name'];
                        $email = $row['email'];
                        $child_id = $row['child_id'];
                    } else {
                        $message = 'Error: Parent not found!';
                    }
                } else {
                    $message = 'Error fetching parent details!';
                }
            } else {
                $message = 'Error: Parent ID not provided!';
            }
            break;
    }
}

// Search functionality
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM parents WHERE parent_name LIKE '%$search%' OR parent_id LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM parents";
}
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Parents</title>
    <link rel="stylesheet" href="manage.css">
</head>
<body>

<h2>Parent Management System</h2>

<!-- Display operation message -->
<?php if ($message != ''): ?>
    <p><?= $message; ?></p>
<?php endif; ?>

<!-- Search Bar -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by Parent Name or ID">
    <input type="submit" value="Search">
</form>

<!-- Parent Form -->
<form method="POST">
    <input type="hidden" name="parent_id" value="<?= $parent_id ?? ''; ?>">
    Parent Name: <input type="text" name="parent_name" value="<?= $parent_name ?? ''; ?>"><br>
    Email: <input type="text" name="email" value="<?= $email ?? ''; ?>"><br>
    Child ID: <input type="text" name="child_id" value="<?= $child_id ?? ''; ?>"><br>
    <?php if(isset($_POST['action']) && $_POST['action'] == 'Edit'): ?>
        <input type="submit" name="action" value="Update">
    <?php else: ?>
        <input type="submit" name="action" value="Add">
    <?php endif; ?>
</form>

<!-- Parents List -->
<h3>Parents List</h3>
<table border="1">
    <tr>
        <th>Parent ID</th>
        <th>Parent Name</th>
        <th>Email</th>
        <th>Child ID</th>
        <th>Actions</th>
    </tr>
    <?php 
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()): 
    ?>
        <tr>
            <td><?= $row['parent_id']; ?></td>
            <td><?= $row['parent_name']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $row['child_id']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="parent_id" value="<?= $row['parent_id']; ?>">
                    <input type="submit" name="action" value="Delete">
                    <input type="hidden" name="parent_id" value="<?= $row['parent_id']; ?>">
                    <input type="submit" name="action" value="Edit">
                </form>
            </td>
        </tr>
    <?php 
            endwhile; 
        } else {
            echo "<tr><td colspan='5'>No parents found</td></tr>";
        }
    ?>
</table>

</body>
</html>
