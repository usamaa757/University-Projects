<?php
include("navbar.php");
include("db.php");

$msg = $error = "";
$plan_id = "";
$title = "";
$description = "";
$calories = "";
$meal_time = "";
$button_text = "Add Diet Plan";
$trainer_id = $_SESSION['user_id'];

// -------------------------
// Delete Diet Plan
// -------------------------
if(isset($_GET['delete_plan'])){
    $del_id = $_GET['delete_plan'];
    mysqli_query($conn, "DELETE FROM diet_plans WHERE id='$del_id'");
    $msg = "Diet Plan deleted successfully.";
    header("Location: add_diet_plan.php?msg=".urlencode($msg));
    exit();
}

// -------------------------
// Edit Diet Plan
// -------------------------
if(isset($_GET['edit_plan'])){
    $plan_id = $_GET['edit_plan'];
    $res = mysqli_query($conn, "SELECT * FROM diet_plans WHERE id='$plan_id'");
    if(mysqli_num_rows($res) > 0){
        $plan = mysqli_fetch_assoc($res);
        $title = $plan['title'];
        $description = $plan['description'];
        $calories = $plan['calories'];
        $meal_time = $plan['meal_time'];
        $button_text = "Update Diet Plan";
    }
}

// -------------------------
// Add / Update Diet Plan
// -------------------------
if(isset($_POST['submit_plan'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $calories = $_POST['calories'];
    $meal_time = $_POST['meal_time'];
    $plan_id = $_POST['plan_id'];

    if($plan_id != ""){
        $sql = "UPDATE diet_plans 
                SET title='$title', description='$description', calories='$calories', meal_time='$meal_time' 
                WHERE id='$plan_id' AND trainer_id = '$trainer_id'";
    } else {
        $sql = "INSERT INTO diet_plans (title, description, calories, meal_time, trainer_id ) 
                VALUES ('$title','$description','$calories','$meal_time', '$trainer_id')";
    }

    if(mysqli_query($conn, $sql)){
        $msg = ($plan_id != "") ? "Diet Plan updated successfully." : "Diet Plan added successfully.";
        header("Location: add_diet_plan.php?msg=".urlencode($msg));
        exit();
    } else {
        $error = "Error saving Diet Plan.";
    }
}

// Fetch all diet plans
$plans = mysqli_query($conn, "SELECT * FROM diet_plans ORDER BY id DESC");

// Check GET message
if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
}
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
        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
        <input type="text" name="title" placeholder="Title" value="<?php echo $title; ?>" required>
        <textarea name="description" placeholder="Description" required><?php echo $description; ?></textarea>
        <input type="number" name="calories" placeholder="Calories (optional)" value="<?php echo $calories; ?>">
        <input type="text" name="meal_time" placeholder="Meal Timing (e.g., Breakfast, Lunch)" value="<?php echo $meal_time; ?>">

        <div class="text-center">
            <button type="submit" name="submit_plan"><?php echo $button_text; ?></button>
        </div>
    </form>
</div>

<div class="table-container">
    <h3>Existing Diet Plans</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Calories</th>
            <th>Meal Time</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($plans)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['calories']; ?></td>
            <td><?php echo $row['meal_time']; ?></td>
            <td>
                <a href="add_diet_plan.php?edit_plan=<?php echo $row['id']; ?>" class="edit">Edit</a>
                <a href="add_diet_plan.php?delete_plan=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="delete">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
