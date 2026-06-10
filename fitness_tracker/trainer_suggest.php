<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "trainer"){
    header("Location: login.php");
    exit();
}

$trainer_id = $_SESSION['user_id'];
$msg = "";

// Get user list
$users = mysqli_query($conn, "SELECT * FROM users WHERE role='user' ORDER BY full_name ASC");

// When trainer selects a specific user
$selected_user = isset($_GET['user_id']) ? $_GET['user_id'] : "";

// Save suggestion
if(isset($_POST['submit_suggestion'])){
    $user_id = $_POST['user_id'];
    $suggestion = $_POST['suggestion'];

    mysqli_query($conn, 
        "INSERT INTO trainer_suggestions (trainer_id, user_id, suggestion)
         VALUES ('$trainer_id', '$user_id', '$suggestion')");

    $msg = "Suggestion submitted successfully!";
}
?>

<div class="table-container">
    <h2>Give Suggestions to Users</h2>

    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <!-- Select User -->
    <form method="GET">
        <select name="user_id" required onchange="this.form.submit()">
            <option value="">Select a User</option>
            <?php while($u = mysqli_fetch_assoc($users)) { ?>
                <option value="<?php echo $u['id']; ?>" 
                    <?php if($selected_user == $u['id']) echo "selected"; ?>>
                    <?php echo $u['full_name']; ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <?php if($selected_user != "") { ?>

        <h3>Recent Activity Logs</h3>

        <?php
        // Fetch logs
        $workouts = mysqli_query($conn, "SELECT * FROM daily_workouts WHERE user_id='$selected_user' ORDER BY log_date DESC LIMIT 10");
        $meals = mysqli_query($conn, "SELECT * FROM daily_meals WHERE user_id='$selected_user' ORDER BY log_date DESC LIMIT 10");
        $water = mysqli_query($conn, "SELECT * FROM daily_water WHERE user_id='$selected_user' ORDER BY log_date DESC LIMIT 10");
        ?>

        <div class="log-box">
            <h4>Workouts</h4>
            <?php while($w = mysqli_fetch_assoc($workouts)){ ?>
                <p><?php echo $w['reps_or_duration']." - ".$w['log_date']; ?></p>
            <?php } ?>
        </div>

        <div class="log-box">
            <h4>Meals</h4>
            <?php while($m = mysqli_fetch_assoc($meals)){ ?>
                <p><?php echo $m['meal_type']." - ".$m['description']." (".$m['calories']." cal)"; ?></p>
            <?php } ?>
        </div>

        <div class="log-box">
            <h4>Water Intake</h4>
            <?php while($wt = mysqli_fetch_assoc($water)){ ?>
                <p><?php echo $wt['amount_ml']." ml - ".$wt['log_date']; ?></p>
            <?php } ?>
        </div>

        <!-- Suggestion Form -->
        <h3>Write Suggestion</h3>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $selected_user; ?>">
            <textarea name="suggestion" placeholder="Write your suggestion..." required></textarea>

            <div class="text-center">

                <button type="submit" name="submit_suggestion">Submit Suggestion</button>
            </div>
        </form>

    <?php } ?>

</div>
