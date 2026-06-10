<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../other/login.php');
    exit();
}

// Fetch the admin's name from the session
$admin_name = isset($_SESSION['user_data']['admin_name']) ? $_SESSION['user_data']['admin_name'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #15260f;
            color: white;
            padding: 10px;
        }

        .menu-icon {
            cursor: pointer;
        }

        .logo {
            font-size: 24px;
        }

        .user-info {
            font-size: 18px;
            margin-right: 20px;
        }

        .navbar {
            width: 250px;
            background-color: #15260f;
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            padding-top: 20px;
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
        }

        .navbar ul li {
            padding: 15px;
            cursor: pointer;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .submenu {
            display: none;
        }

        .submenu.open {
            display: block;
        }

        .arrow {
            margin-left: 5px;
        }

        .navbar ul li:hover {
            background-color: #575757;
        }

        .navbar ul li a:hover {
            text-decoration: none;
            padding: 15px;
        }

        .open {
            display: block;
        }

    </style>
</head>

<body>

    <!-- Top bar -->
    <div class="top-bar">
        <!-- Hamburger menu icon -->
        <div class="menu-icon" onclick="toggleNavbar()">
            &#9776;
        </div>
        <div class="logo"><i class="fas fa-school"></i> School Automation System</div>
        <div class="user-info">
            <span style="margin-right: 60px;">Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <br><br><ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>

            <li>
                <a href="teacher_attendance.php" onclick="toggleSubmenu('attendance')">
                    <i class="fas fa-user-graduate"></i> Teacher Attendance 
                </a>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu('students')">
                    <i class="fas fa-users"></i> Students <span class="arrow">&#9662;</span>
                </a>
                <ul class="submenu" id="students_submenu">
                    <li><a href="list_students.php">View Student Registrations</a></li>
                    <li><a href="manage_students.php">Manage Students</a></li>
                    <li><a href="view_students.php">View Students</a></li>
                    <li><a href="student_attendance_record.php">Attendance Recored</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu('teachers')">
                    <i class="fas fa-chalkboard-teacher"></i> Teachers <span class="arrow">&#9662;</span>
                </a>
                <ul class="submenu" id="teachers_submenu">
                    <li><a href="list_teachers.php">View Teacher Registrations</a></li>
                    <li><a href="add_teachers.php">Add Teachers</a></li>
                    <li><a href="manage_teachers.php">Manage Teachers</a></li>
                    <li><a href="add_teacher_class_course.php">Add Teacher's Class and Course</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu('parents')">
                    <i class="fas fa-users"></i> Parents <span class="arrow">&#9662;</span>
                </a>
                <ul class="submenu" id="parents_submenu">
                    <li><a href="list_parents.php">View Parent Registrations</a></li>
                    <li><a href="add_parents.php">Add Parents</a></li>
                    <li><a href="view_parents.php">View Parents</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu('classes')">
                    <i class="fas fa-school"></i> Classes <span class="arrow">&#9662;</span>
                </a>
                <ul class="submenu" id="classes_submenu">
                    <li><a href="manage_classes.php">Manage Classes</a></li>
                    <li><a href="view_classes.php">View Classes</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu('fee')">
                    <i class="fas fa-money-bill-wave"></i> Fee <span class="arrow">&#9662;</span>
                </a>
                <ul class="submenu" id="fee_submenu">
                    <li><a href="create_voucher.php">Create Vouchers</a></li>
                    <li><a href="view_voucher.php">View Fee</a></li>
                </ul>
            </li>
            
            <li>
                <a href="#" onclick="toggleSubmenu('courses')">
                    <i class="fas fa-book-open"></i> Courses <span class="arrow">&#9662;</span>
                </a>
                <ul class="submenu" id="courses_submenu">
                    <li><a href="add_course.php">Add Courses</a></li>
                    <li><a href="view_course.php">View Courses</a></li>
                    <li><a href="assign_course_to_class.php">Assign Course to Class</a></li>

                </ul>
            </li>           
            <li><a href="degree_dmc.php"><i class="fas fa-chart-line"></i> DMC & Degree</a></li>
            <li><a href="salary_slip.php"><i class="fas fa-money-bill"></i> Salary</a></li>
            <li><a href="../other/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }

        // Function to toggle submenu
        function toggleSubmenu(submenuId) {
            var submenu = document.getElementById(submenuId + "_submenu");
            submenu.classList.toggle("open");
        }
    </script>