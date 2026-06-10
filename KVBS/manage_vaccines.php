<?php
include("header.php");
include("db_connect.php");

$message = "";
$error = "";

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM vaccines WHERE id=$id";
    if (mysqli_query($conn, $delete_query)) {
        $message = "Vaccine deleted successfully!";
    } else {
        $error = "Error deleting vaccine: " . mysqli_error($conn);
    }
}

// --- Handle Add / Update ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vaccine_name = trim($_POST['vaccine_name']);
    $description = trim($_POST['description']);
    $age_group = trim($_POST['age_group']);
    $dose_count = trim($_POST['dose_count']);

    if (isset($_POST['id']) && $_POST['id'] != "") {
        // Update existing record
        $id = $_POST['id'];
        $update_sql = "UPDATE vaccines SET 
                        vaccine_name='$vaccine_name',
                        description='$description',
                        age_group='$age_group',
                        dose_count='$dose_count'
                        WHERE id='$id'";
        if (mysqli_query($conn, $update_sql)) {
            $message = "Vaccine updated successfully!";
        } else {
            $error = "Error updating record: " . mysqli_error($conn);
        }
    } else {
        // Add new vaccine
        $check = mysqli_query($conn, "SELECT * FROM vaccines WHERE vaccine_name='$vaccine_name'");
        if (mysqli_num_rows($check) > 0) {
            $error = "This vaccine already exists!";
        } else {
            $query = "INSERT INTO vaccines (vaccine_name, description, age_group, dose_count)
                      VALUES ('$vaccine_name', '$description', '$age_group', '$dose_count')";
            if (mysqli_query($conn, $query)) {
                $message = "Vaccine added successfully!";
            } else {
                $error = "Error adding vaccine: " . mysqli_error($conn);
            }
        }
    }
}

// --- Fetch All Vaccines ---
$result = mysqli_query($conn, "SELECT * FROM vaccines ORDER BY id DESC");

// --- Edit Mode ---
$edit_vaccine = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM vaccines WHERE id=$id");
    $edit_vaccine = mysqli_fetch_assoc($res);
}
?>



<div class="management-container" style="max-width: 1300px;">
    <h1>Manage Vaccines</h1>


    <a class="back" href="dashboard.php">← Back to Dashboard</a>

    <h2><?php echo $edit_vaccine ? "Edit Vaccine" : "Add New Vaccine"; ?></h2>

    <?php
    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } else {
        echo "<div class='message'>$message</div>";
    }
    ?>

    <form method="POST" class="form">
        <input type="hidden" name="id" value="<?php echo $edit_vaccine['id'] ?? ''; ?>">

        <input type="text" name="vaccine_name" placeholder="Vaccine Name"
            value="<?php echo $edit_vaccine['vaccine_name'] ?? ''; ?>" required>
        <input type="text" name="age_group" placeholder="Age Group (e.g. 0-5 years)"
            value="<?php echo $edit_vaccine['age_group'] ?? ''; ?>">
        <input type="number" name="dose_count" placeholder="Dose Count"
            value="<?php echo $edit_vaccine['dose_count'] ?? ''; ?>">
        <textarea name="description"
            placeholder="Description (details about the vaccine)"><?php echo $edit_vaccine['description'] ?? ''; ?></textarea>

        <div style="grid-column: span 2; text-align:center;">
            <button type="submit"><?php echo $edit_vaccine ? "Update Vaccine" : "Add Vaccine"; ?></button>
        </div>
    </form>

    <h2>All Vaccines</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Vaccine Name</th>
            <th>Age Group</th>
            <th>Dose Count</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['vaccine_name']; ?></td>
            <td><?php echo $row['age_group']; ?></td>
            <td><?php echo $row['dose_count']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td>
                <a class="action edit" href="manage_vaccines.php?edit=<?php echo $row['id']; ?>">Edit</a>
                <a class="action delete" href="manage_vaccines.php?delete=<?php echo $row['id']; ?>"
                    onclick="return confirm('Are you sure you want to delete this vaccine?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php

include('footer.php');

?>
</body>

</html>