<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = $error = "";

// -------------------------
// LOG WORKOUT
// -------------------------
if(isset($_POST['log_workout'])){
    $workout_id = $_POST['workout_id'];
    $reps_or_duration = $_POST['reps_or_duration'];
    $log_date = date('Y-m-d');

    $sql = "INSERT INTO daily_workouts (user_id, workout_id, reps_or_duration, log_date)
            VALUES ('$user_id','$workout_id','$reps_or_duration','$log_date')";
    if(mysqli_query($conn, $sql)){
        $msg = "Workout logged successfully.";
    } else {
        $error = "Error logging workout.";
    }
}

// -------------------------
// LOG MEAL
// -------------------------
if(isset($_POST['log_meal'])){
    $meal_type = $_POST['meal_type'];
    $description = $_POST['description'];
    $calories = $_POST['calories'];
    $log_date = date('Y-m-d');

    $sql = "INSERT INTO daily_meals (user_id, meal_type, description, calories, log_date)
            VALUES ('$user_id','$meal_type','$description','$calories','$log_date')";
    if(mysqli_query($conn, $sql)){
        $msg = "Meal logged successfully.";
    } else {
        $error = "Error logging meal.";
    }
}

// -------------------------
// LOG WATER
// -------------------------
if(isset($_POST['log_water'])){
    $amount_ml = $_POST['amount_ml'];
    $log_date = date('Y-m-d');

    $sql = "INSERT INTO daily_water (user_id, amount_ml, log_date)
            VALUES ('$user_id','$amount_ml','$log_date')";
    if(mysqli_query($conn, $sql)){
        $msg = "Water intake logged successfully.";
    } else {
        $error = "Error logging water.";
    }
}

// -------------------------
// FETCH WORKOUT ROUTINES
// -------------------------
$workouts = mysqli_query($conn, "SELECT * FROM workout_routines ORDER BY title ASC");
?>

<div class="activity-container">
    <a href="progress.php">Progress</a>
    <h2>Daily Activities</h2>

    <?php if($error != "") { ?><p class="error"><?php echo $error; ?></p><?php } ?>
    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <div class="activity-forms">
        <!-- Log Workout -->
        <div class="form-container">
            <h3>Log Workout</h3>
            <form method="POST">
                <select name="workout_id" required>
                    <option value="">Select Workout</option>
                    <?php while($w = mysqli_fetch_assoc($workouts)) { ?>
                        <option value="<?php echo $w['id']; ?>"><?php echo $w['title']." (".$w['difficulty'].")"; ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="reps_or_duration" placeholder="Reps or Duration" required>
                <div class="text-center">
                    <button type="submit" name="log_workout">Log Workout</button>
                </div>
            </form>
        </div>

        <!-- Log Meal -->
        <div class="form-container">
            <h3>Log Meal</h3>
            <form method="POST">
                <select name="meal_type" required>
                    <option value="">Select Meal Type</option>
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="Dinner">Dinner</option>
                    <option value="Snack">Snack</option>
                </select>
                <textarea name="description" placeholder="Meal description" required></textarea>
                <input type="number" name="calories" placeholder="Calories (optional)">
                <div class="text-center">
                    <button type="submit" name="log_meal">Log Meal</button>
                </div>
            </form>
        </div>

        <!-- Log Water -->
        <div class="form-container">
            <h3>Log Water Intake</h3>
            <form method="POST">
                <input type="number" name="amount_ml" placeholder="Amount in ml" required>
                <div class="text-center">
                    <button type="submit" name="log_water">Log Water</button>
                </div>
            </form>
        </div>
    </div>
</div>

