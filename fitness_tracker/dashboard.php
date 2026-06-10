<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$name = $_SESSION['full_name'];
?>

<div class="dashboard-container">

    <div class="dashboard-title">
        👋 Welcome, <?php echo $name; ?> <br>
        <small style="font-size:16px; color:#444;">Role: <?php echo ucfirst($role); ?></small>
    </div>

    <div class="grid">

        <!-- ======= ADMIN ======= -->
        <?php if($role == "admin") { ?>

            <div class="card">
                <div class="icon">👤</div>
                <h3>Manage Users</h3>
                <p>View, add, update, and delete user accounts.</p>
                <a href="manage_users.php">Open</a>
            </div>

            <div class="card">
                <div class="icon">💡</div>
                <h3>Manage Tips</h3>
                <p>Create and manage fitness tips.</p>
                <a href="add_tips.php">Open</a>
            </div>

            <div class="card">
                <div class="icon">🏋️‍♂️</div>
                <h3>Manage Workout Routines</h3>
                <p>Create and update exercise routines.</p>
                <a href="add_routines.php">Open</a>
            </div>

            <div class="card">
                <div class="icon">🥗</div>
                <h3>Manage Diet Plans</h3>
                <p>Create diet plans for your users.</p>
                <a href="add_diet_plan.php">Open</a>
            </div>

            <div class="card">
                <div class="icon">📊</div>
                <h3>User Logs & Feedback</h3>
                <p>Monitor all user activities and feedback.</p>
                <a href="view_user_activity.php">Open</a>
            </div>

        <?php } ?>

        <!-- ======= TRAINER ======= -->
        <?php if($role == "trainer") { ?>

            <div class="card">
                <div class="icon">💬</div>
                <h3>User Workout Feedback</h3>
                <p>View feedback on workout routines you assigned.</p>
                <a href="view_workout_feedback.php">View</a>
            </div>

            <div class="card">
                <div class="icon">📄</div>
                <h3>User Diet Feedback</h3>
                <p>Check comments and responses on diet plans.</p>
                <a href="trainer_view_diet_feedback.php">View</a>
            </div>

            <div class="card">
                <div class="icon">🏋️‍♂️</div>
                <h3>Suggest Workout Plans</h3>
                <p>Recommend custom workout routines to users.</p>
                <a href="add_routines.php">Suggest</a>
            </div>

            <div class="card">
                <div class="icon">🥗</div>
                <h3>Suggest Diet Plans</h3>
                <p>Create and assign diet plans to users.</p>
                <a href="add_diet_plan.php">Suggest</a>
            </div>

            <div class="card">
                <div class="icon">💡</div>
                <h3>Trainer Suggestions</h3>
                <p>Send personalized recommendations based on activity logs.</p>
                <a href="trainer_suggest.php">Suggest</a>
            </div>

        <?php } ?>

        <!-- ======= USER ======= -->
        <?php if($role == "user") { ?>

            <div class="card">
                <div class="icon">🏃‍♂️</div>
                <h3>Add Activity</h3>
                <p>Log your workouts, meals, and water intake.</p>
                <a href="add_activity.php">Add Activity</a>
            </div>

            <div class="card">
                <div class="icon">🏋️‍♀️</div>
                <h3>Workout Routines</h3>
                <p>Your trainer-assigned workout plans.</p>
                <a href="view_routines.php">View Plans</a>
            </div>

            <div class="card">
                <div class="icon">🥗</div>
                <h3>Diet Plans</h3>
                <p>View diet plans and send feedback to your trainer.</p>
                <a href="view_diet_plans.php">View Diets</a>
            </div>

            <div class="card">
                <div class="icon">📊</div>
                <h3>Progress</h3>
                <p>Track your weight, calories, and activity trends.</p>
                <a href="progress.php">View Progress</a>
            </div>

            <div class="card">
                <div class="icon">💬</div>
                <h3>Trainer Suggestions</h3>
                <p>Receive personalized plans and advice from your trainer.</p>
                <a href="view_suggestions.php">View Suggestions</a>
            </div>
            <div class="card">
                <div class="icon">💡</div>
                <h3>Fitness Tips</h3>
                <p>Explore expert fitness, nutrition, and wellness tips.</p>
                <a href="view_tips.php">View Tips</a>
            </div>
        <?php } ?>

    </div>
</div>
