<?php
include("navbar.php");
include("db.php");

$msg = $error = "";
$tip_id = "";
$title = "";
$description = "";
$button_text = "Add Tip";

// -------------------------
// Delete Tip
// -------------------------
if(isset($_GET['delete_tip'])){
    $del_id = $_GET['delete_tip'];
    mysqli_query($conn, "DELETE FROM fitness_tips WHERE id='$del_id'");
    $msg = "Tip deleted successfully.";
    header("Location: manage_content.php?msg=".urlencode($msg));
    exit();
}

// -------------------------
// Edit Tip
// -------------------------
if(isset($_GET['edit_tip'])){
    $tip_id = $_GET['edit_tip'];
    $res = mysqli_query($conn, "SELECT * FROM fitness_tips WHERE id='$tip_id'");
    if(mysqli_num_rows($res) > 0){
        $tip = mysqli_fetch_assoc($res);
        $title = $tip['title'];
        $description = $tip['description'];
        $button_text = "Update Tip";
    }
}

// -------------------------
// Add / Update Tip
// -------------------------
if(isset($_POST['submit_tip'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $tip_id = $_POST['tip_id'];

    if($tip_id != ""){
        $sql = "UPDATE fitness_tips SET title='$title', description='$description' WHERE id='$tip_id'";
        if(mysqli_query($conn, $sql)){
            $msg = "Tip updated successfully.";
            header("Location: manage_content.php?msg=".urlencode($msg));
            exit();
        } else {
            $error = "Error updating tip.";
        }
    } else {
        $sql = "INSERT INTO fitness_tips (title, description) VALUES ('$title', '$description')";
        if(mysqli_query($conn, $sql)){
            $msg = "Tip added successfully.";
            $title = $description = "";
        } else {
            $error = "Error adding tip.";
        }
    }
}

// Fetch all tips
$tips = mysqli_query($conn, "SELECT * FROM fitness_tips ORDER BY id DESC");
?>
<div class="form-container">
    <h2><?php echo $button_text; ?></h2>

    <?php if($error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <?php if($msg != "") { ?>
        <p class="message"><?php echo $msg; ?></p>
    <?php } ?>

    <form method="POST">
        <input type="hidden" name="tip_id" value="<?php echo $tip_id; ?>">
        <input type="text" name="title" placeholder="Title" value="<?php echo $title; ?>" required>
        <textarea name="description" placeholder="Description" required><?php echo $description; ?></textarea>

        <div class="text-center">
            <button type="submit" name="submit_tip"><?php echo $button_text; ?></button>
        </div>
    </form>
</div>
<div class="table-container">
    <h3>Existing Tips</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($tips)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td>
                <a href="manage_content.php?edit_tip=<?php echo $row['id']; ?>" class="edit">Edit</a>
                <a href="manage_content.php?delete_tip=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="delete">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
