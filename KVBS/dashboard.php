<?php
include("header.php");
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_name = $_SESSION['user_name'];
?>

<div class="dashboard-wrapper" style="margin-bottom: 113px;">
    <div class="dashboard-header">
        <?php if ($role == 'admin') { ?>
        <h1><i class="fa-solid fa-user-shield"></i> Admin Dashboard</h1>
        <?php } elseif ($role == 'parent') { ?>
        <h1><i class="fa-solid fa-people-roof"></i> Parent Dashboard</h1>
        <?php } else { ?>
        <h1><i class="fa-solid fa-user-nurse"></i> Health Worker Dashboard</h1>
        <?php } ?>
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
    </div>

    <div class="dashboard-grid">

        <?php if ($role == 'admin') { ?>
        <a href="manage_users.php" class="dash-card">
            <i class="fa-solid fa-user-doctor"></i>
            <span>Manage Health Workers</span>
        </a>
        <a href="manage_vaccines.php" class="dash-card">
            <i class="fa-solid fa-syringe"></i>
            <span>Manage Vaccines</span>
        </a>

        <a href="manage_bookings.php" class="dash-card">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Manage Bookings</span>
        </a>
        <a href="reports.php" class="dash-card">
            <i class="fa-solid fa-chart-line"></i>
            <span>Generate Reports</span>
        </a>

        <?php } elseif ($role == 'parent') { ?>
        <a href="manage_childrens.php" class="dash-card">
            <i class="fa-solid fa-children"></i>
            <span>Manage Children</span>
        </a>
        <a href="book_vaccine.php" class="dash-card">
            <i class="fa-solid fa-notes-medical"></i>
            <span>Book Vaccination Visit</span>
        </a>
        <a href="booking_list.php" class="dash-card">
            <i class="fa-solid fa-calendar-days"></i>
            <span>My Bookings</span>
        </a>

        <?php } elseif ($role == 'worker') { ?>
        <a href="assigned_booking.php" class="dash-card">
            <i class="fa-solid fa-briefcase-medical"></i>
            <span>Assigned Bookings</span>
        </a>
        <a href="reports.php" class="dash-card">
            <i class="fa-solid fa-chart-pie"></i>
            <span>My Reports</span>
        </a>
        <?php } ?>

        <a href="profile.php" class="dash-card">
            <i class="fa-solid fa-user-gear"></i>
            <span>Edit Profile</span>
        </a>
    </div>
</div>
<?php

include('footer.php');

?>
</body>

</html>