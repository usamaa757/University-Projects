<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// TODAY'S DATE
$today = date("Y-m-d");

// ===== FETCH WORKOUT DATA (group by date) =====
$workout_data = [];
$q1 = mysqli_query($conn, "
    SELECT log_date, COUNT(*) AS total_workouts
    FROM daily_workouts
    WHERE user_id = '$user_id'
    GROUP BY log_date
    ORDER BY log_date ASC
");
while($row = mysqli_fetch_assoc($q1)){
    $workout_data[] = $row;
}

// ===== FETCH MEAL (CALORIES) DATA =====
$meal_data = [];
$q2 = mysqli_query($conn, "
    SELECT log_date, SUM(calories) AS total_calories
    FROM daily_meals
    WHERE user_id = '$user_id'
    GROUP BY log_date
    ORDER BY log_date ASC
");
while($row = mysqli_fetch_assoc($q2)){
    $meal_data[] = $row;
}

// ===== FETCH WATER INTAKE DATA =====
$water_data = [];
$q3 = mysqli_query($conn, "
    SELECT log_date, SUM(amount_ml) AS total_water
    FROM daily_water
    WHERE user_id = '$user_id'
    GROUP BY log_date
    ORDER BY log_date ASC
");
while($row = mysqli_fetch_assoc($q3)){
    $water_data[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Progress Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .progress-container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }

        h2 {
            font-size: 28px;
            color: #0d1b2a;
            margin-bottom: 10px;
            text-align: center;
        }

        .chart-box {
            background: #fff;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 14px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.08);
        }

        canvas {
            width: 100% !important;
            height: 350px !important;
        }
    </style>
</head>

<body>

<div class="progress-container">

    <h2>Your Fitness Progress 📊</h2>

    <!-- WORKOUT CHART -->
    <div class="chart-box">
        <h3>Workout Activity</h3>
        <canvas id="workoutChart"></canvas>
    </div>

    <!-- CALORIES CHART -->
    <div class="chart-box">
        <h3>Calories Intake</h3>
        <canvas id="caloriesChart"></canvas>
    </div>

    <!-- WATER CHART -->
    <div class="chart-box">
        <h3>Water Intake (ml)</h3>
        <canvas id="waterChart"></canvas>
    </div>

</div>

<script>
// Convert PHP → JS arrays
const workoutDates = <?php echo json_encode(array_column($workout_data, 'log_date')); ?>;
const workoutCount = <?php echo json_encode(array_column($workout_data, 'total_workouts')); ?>;

const mealDates = <?php echo json_encode(array_column($meal_data, 'log_date')); ?>;
const mealCalories = <?php echo json_encode(array_column($meal_data, 'total_calories')); ?>;

const waterDates = <?php echo json_encode(array_column($water_data, 'log_date')); ?>;
const waterAmounts = <?php echo json_encode(array_column($water_data, 'total_water')); ?>;

// WORKOUT CHART
new Chart(document.getElementById('workoutChart'), {
    type: 'line',
    data: {
        labels: workoutDates,
        datasets: [{
            label: 'Total Workouts',
            data: workoutCount,
            borderWidth: 2
        }]
    }
});

// CALORIES CHART
new Chart(document.getElementById('caloriesChart'), {
    type: 'bar',
    data: {
        labels: mealDates,
        datasets: [{
            label: 'Total Calories',
            data: mealCalories,
            borderWidth: 2
        }]
    }
});

// WATER INTAKE CHART
new Chart(document.getElementById('waterChart'), {
    type: 'line',
    data: {
        labels: waterDates,
        datasets: [{
            label: 'Water (ml)',
            data: waterAmounts,
            borderWidth: 2
        }]
    }
});
</script>

</body>
</html>
