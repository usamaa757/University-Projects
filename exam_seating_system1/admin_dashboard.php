<?php
include 'db.php';
include 'header.php';
include 'admin_auth.php';

// Total number of students
$student_query = mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM students");
$student_data = mysqli_fetch_assoc($student_query);
$total_students = $student_data['total_students'];

// Total number of courses
$course_query = mysqli_query($conn, "SELECT COUNT(*) AS total_courses FROM courses");
$course_data = mysqli_fetch_assoc($course_query);
$total_courses = $course_data['total_courses'];

// Total number of upcoming seats
$seat_query = mysqli_query($conn, "SELECT COUNT(*) AS total_seats FROM seat_assignments");
$seat_data = mysqli_fetch_assoc($seat_query);
$total_seats = $seat_data['total_seats'];
?>



<h3>Admin Dashboard</h3>
<div class="dashboard-container">
    <div class="dashboard-box">
        <h3>Total Students</h3>
        <p><?php echo $total_students; ?></p>
    </div>

    <div class="dashboard-box">
        <h3>Total Courses</h3>
        <p><?php echo $total_courses; ?></p>
    </div>

    <div class="dashboard-box">
        <h3>Assigned Seats</h3>
        <p><?php echo $total_seats; ?></p>
    </div>
</div>