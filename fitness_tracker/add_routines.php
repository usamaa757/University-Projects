<?php
include("navbar.php");
include("db.php");

$msg = $error = "";
$routine_id = "";
$title = "";
$description = "";
$difficulty = "";
$duration = "";
$button_text = "Add Routine";
$trainer_id = $_SESSION['user_id'];
// -------------------------
// DELETE ROUTINE
// -------------------------
if(isset($_GET['delete_id'])){
    $del_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM workout_routines WHERE id='$del_id'");
    $msg = "Workout Routine deleted successfully.";
    header("Location: add_routines.php?msg=".urlencode($msg));
    exit();
}

// -------------------------
// EDIT ROUTINE
// -------------------------
if(isset($_GET['edit_id'])){
    $routine_id = $_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT * FROM workout_routines WHERE id='$routine_id'");
    if(mysqli_num_rows($res) > 0){
        $routine = mysqli_fetch_assoc($res);
        $title = $routine['title'];
        $description = $routine['description'];
        $difficulty = $routine['difficulty'];
        $duration = $routine['duration'];
        $button_text = "Update Routine";
    }
}

// -------------------------
// ADD / UPDATE ROUTINE
// -------------------------
if(isset($_POST['submit_routine'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $difficulty = $_POST['difficulty'];
    $duration = $_POST['duration'];
    $routine_id = $_POST['routine_id'];

    if($routine_id != ""){
        $sql = "UPDATE workout_routines SET title='$title', description='$description', difficulty='$difficulty', duration='$duration' WHERE id='$routine_id' AND trainer_id ='$trainer_id'";
    } else {
        $sql = "INSERT INTO workout_routines (title, description, difficulty, duration,  trainer_id) VALUES ('$title','$description','$difficulty','$duration', '$trainer_id')";
    }

    if(mysqli_query($conn, $sql)){
        $msg = ($routine_id != "") ? "Workout Routine updated successfully." : "Workout Routine added successfully.";
        header("Location: add_routines.php?msg=".urlencode($msg));
        exit();
    } else {
        $error = "Error saving Workout Routine.";
    }
}

// -------------------------
// FETCH ALL ROUTINES
// -------------------------
$routines = mysqli_query($conn, "SELECT * FROM workout_routines ORDER BY id DESC");

// Check GET message
if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
}
?>

<!-- ------------------------- -->
<!-- ROUTINE FORM -->
<!-- ------------------------- -->
<div class="form-container">
    <h2><?php echo $button_text; ?></h2>

    <?php if($error != "") { ?><p class="error"><?php echo $error; ?></p><?php } ?>
    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <form method="POST">
        <input type="hidden" name="routine_id" value="<?php echo $routine_id; ?>">

        <input type="text" name="title" placeholder="Title" value="<?php echo $title; ?>" required>

        <textarea name="description" placeholder="Description (Exercises, sets, reps)" required><?php echo $description; ?></textarea>

        <select name="difficulty" required>
            <option value="">Select Difficulty</option>
            <option value="Beginner" <?php if($difficulty=="Beginner") echo 'selected'; ?>>Beginner</option>
            <option value="Intermediate" <?php if($difficulty=="Intermediate") echo 'selected'; ?>>Intermediate</option>
            <option value="Advanced" <?php if($difficulty=="Advanced") echo 'selected'; ?>>Advanced</option>
        </select>

        <input type="text" name="duration" placeholder="Duration (e.g., 45 minutes)" value="<?php echo $duration; ?>">

        <div class="text-center">
            <button type="submit" name="submit_routine"><?php echo $button_text; ?></button>
        </div>
    </form>
</div>

<!-- ------------------------- -->
<!-- ROUTINES TABLE -->
<!-- ------------------------- -->
<div class="table-container">
    <h3>Existing Workout Routines</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Difficulty</th>
            <th>Duration (Minutes)</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($routines)){ ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['difficulty']; ?></td>
            <td><?php echo $row['duration']; ?></td>
            <td>
                <a href="add_routines.php?edit_id=<?php echo $row['id']; ?>" class="edit">Edit</a>
                <a href="add_routines.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="delete">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
