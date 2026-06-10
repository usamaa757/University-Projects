<?php
include("db_connect.php");
include("header.php");


$parent_id = $_SESSION['user_id'];
$message = "";
$error = "";

// --- Edit Mode ---
$edit_child = null;
if (isset($_GET['edit'])) {
    $child_id = $_GET['edit'];
    $result_edit = mysqli_query($conn, "SELECT * FROM children WHERE id='$child_id' AND parent_id='$parent_id'");
    if (mysqli_num_rows($result_edit) > 0) {
        $edit_child = mysqli_fetch_assoc($result_edit);
    } else {
        $error = "Invalid child or unauthorized access!";
    }
}

// --- Add / Update Child ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_child'])) {
    $child_name = trim($_POST['child_name']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);

    if (!empty($child_name) && !empty($dob) && !empty($gender)) {
        if (!empty($_POST['child_id'])) {
            // Update existing record
            $child_id = $_POST['child_id'];
            $sql = "UPDATE children SET child_name='$child_name', dob='$dob', gender='$gender'
                    WHERE id='$child_id' AND parent_id='$parent_id'";
            if (mysqli_query($conn, $sql)) {
                $message = "Child updated successfully!";
                $edit_child = null; // reset edit form
            } else {
                $error = "Error updating child: " . mysqli_error($conn);
            }
        } else {
            // Add new record
            $sql = "INSERT INTO children (parent_id, child_name, dob, gender)
                    VALUES ('$parent_id', '$child_name', '$dob', '$gender')";
            if (mysqli_query($conn, $sql)) {
                $message = "Child added successfully!";
            } else {
                $error = "Error adding child: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "All fields are required!";
    }
}
// --- Delete Child ---
if (isset($_GET['delete'])) {
    $child_id = $_GET['delete'];

    // Step 1: Delete related bookings
    $delete_bookings = "DELETE FROM bookings WHERE child_id='$child_id' AND parent_id='$parent_id'";
    mysqli_query($conn, $delete_bookings);

    // Step 2: Delete child record
    $delete_child = "DELETE FROM children WHERE id='$child_id' AND parent_id='$parent_id'";
    if (mysqli_query($conn, $delete_child)) {

        $message = "Child deleted successfully!";
    } else {
        $error = "Error deleting child: " . mysqli_error($conn);
    }
}

// --- Fetch Children ---
$result = mysqli_query($conn, "SELECT * FROM children WHERE parent_id='$parent_id' ORDER BY id DESC");
?>

<div class="management-container">
    <a class="back" href="parent_dashboard.php">← Back to Dashboard</a>

    <?php
    if (!empty($message)) echo "<div class='message' style='color:green;'>$message</div>";
    if (!empty($error)) echo "<div class='error' style='color:red;'>$error</div>";
    ?>

    <h2>👶 <?php echo $edit_child ? "Edit Child Details" : "Add New Child"; ?></h2>

    <form method="POST" class="form">
        <input type="hidden" name="child_id" value="<?php echo $edit_child['id'] ?? ''; ?>">
        <input type="text" name="child_name" placeholder="Child Full Name"
            value="<?php echo htmlspecialchars($edit_child['child_name'] ?? ''); ?>" required>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($edit_child['dob'] ?? ''); ?>" required>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php if (($edit_child['gender'] ?? '') == 'Male') echo 'selected'; ?>>Boy</option>
            <option value="Female" <?php if (($edit_child['gender'] ?? '') == 'Female') echo 'selected'; ?>>Girl
            </option>
            <option value="Other" <?php if (($edit_child['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
        </select>
        <div style="grid-column: span 2; text-align:center;">
            <button type="submit" name="save_child">
                <?php echo $edit_child ? "Update Child" : "Add Child"; ?>
            </button>
        </div>
    </form>

    <h2>👧👦 Saved Children</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Action</th>
        </tr>

        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['child_name']); ?></td>
            <td><?php echo htmlspecialchars($row['dob']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td>
                <a class="action edit" href="?edit=<?php echo $row['id']; ?>">Edit</a>
                <a class="action delete" href="?delete=<?php echo $row['id']; ?>"
                    onclick="return confirm('Delete this child?');">Delete</a>
            </td>
        </tr>
        <?php }
        } else { ?>
        <tr>
            <td colspan="5" style="text-align:center;">No children added yet.</td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php

include('footer.php');

?>
</body>

</html>