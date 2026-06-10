<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$msg = "";
if(isset($_GET['msg'])) $msg = $_GET['msg'];

// Fetch user workouts
$workouts = mysqli_query($conn, "
    SELECT w.id, u.full_name, wr.title AS workout, w.reps_or_duration, w.log_date 
    FROM daily_workouts w
    JOIN users u ON w.user_id = u.id
    JOIN workout_routines wr ON w.workout_id = wr.id
    ORDER BY w.log_date DESC
");

// Fetch user meals
$meals = mysqli_query($conn, "
    SELECT m.id, u.full_name, m.meal_type, m.description, m.calories, m.log_date 
    FROM daily_meals m
    JOIN users u ON m.user_id = u.id
    ORDER BY m.log_date DESC
");

// Fetch user water intake
$waters = mysqli_query($conn, "
    SELECT w.id, u.full_name, w.amount_ml, w.log_date 
    FROM daily_water w
    JOIN users u ON w.user_id = u.id
    ORDER BY w.log_date DESC
");
?>

<div class="activity-container">
    <h2>User Activity Logs</h2>

    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <!-- Workouts Logs -->
    <div class="table-container">
        <h3>Workout Logs</h3>
        <table>
            <tr>
                <th>User</th>
                <th>Workout</th>
                <th>Reps/Duration</th>
                <th>Date</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($workouts)){ ?>
            <tr>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['workout']; ?></td>
                <td><?php echo $row['reps_or_duration']; ?></td>
                <td><?php echo $row['log_date']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Meal Logs -->
    <div class="table-container">
        <h3>Meal Logs</h3>
        <table>
            <tr>
                <th>User</th>
                <th>Meal Type</th>
                <th>Description</th>
                <th>Calories</th>
                <th>Date</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($meals)){ ?>
            <tr>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['meal_type']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['calories']; ?></td>
                <td><?php echo $row['log_date']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Water Logs -->
    <div class="table-container">
        <h3>Water Intake Logs</h3>
        <table>
            <tr>
                <th>User</th>
                <th>Amount (ml)</th>
                <th>Date</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($waters)){ ?>
            <tr>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['amount_ml']; ?></td>
                <td><?php echo $row['log_date']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
