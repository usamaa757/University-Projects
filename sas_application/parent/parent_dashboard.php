<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if the user is logged in and is a parent
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parents') {
    header('Location: ../other/login.php');
    exit();
}

// Fetch the parent's name from the session data
$parent_name = $_SESSION['user_data']['parent_name'] ?? 'Parent';

// HTML for the parent dashboard page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System - Parent Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    <!-- Top bar -->
    <div class="top-bar">
        <!-- Hamburger menu icon -->
        <div class="menu-icon" onclick="toggleNavbar()">
            &#9776;
        </div>
        <div class="logo"><i class="fas fa-school fa-1x"></i> School Automation System</div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($parent_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <ul>
            <li><a href="parent_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="feedback.php"><i class="fas fa-comments"></i> Give Feedback</a></li>
            <li><a href="../other/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }
    </script>
</body>

</html>
