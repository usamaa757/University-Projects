<?php
// Start the session
session_start();
$student_id = $_SESSION['student_id'];

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: user-login.php"); // Redirect to login page if not logged in
    exit();
}

// Include your database connection file
include('db_connect.php');

// Fetch notifications from the database for the logged-in student
$query = "SELECT remarks FROM stud_admission WHERE student_id = $student_id";
$result = mysqli_query($conn, $query);

// Initialize notifications array
$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (!empty($row['remarks'])) {
        $notifications[] = $row['remarks'];
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="CSS/stud_dash_style.css">
</head>
<body>

<header>
    <h1>Student Dashboard</h1>
    
    <div class="notification" id="notificationBtn">
        Notifications
        <div class="notification-list" id="notificationList">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <li><?php echo htmlspecialchars($notification); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No remarks</li>
            <?php endif; ?>
        </div>
    </div>
    
    <button onclick="window.location.href='logout.php'">Logout</button>
</header>

<div class="buttons">
    <button onclick="window.location.href='stud_admission.php'">Admission Form</button>
    <button onclick="window.location.href='view_your_form.php'">View Your Form</button>
    <button onclick="window.location.href='user-profile.php'">Your Profile</button>
</div>

<script>
    // Notification toggle
    document.getElementById('notificationBtn').addEventListener('click', function () {
        document.getElementById('notificationList').classList.toggle('active');
    });
</script>

</body>
</html>
