<?php
include 'header.php';

include 'db.php';

// Fetch total students
$student_query = mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM students");
$student_data = mysqli_fetch_assoc($student_query);
$total_students = $student_data['total_students'];

// Fetch total courses
$course_query = mysqli_query($conn, "SELECT COUNT(*) AS total_courses FROM courses");
$course_data = mysqli_fetch_assoc($course_query);
$total_courses = $course_data['total_courses'];
?>


<!-- Dashboard Content -->
<div class="container mt-5">
    <div class="row">
        <!-- Total Students Card -->
        <div class="col-md-6">
            <div class="card shadow-lg text-center p-4">
                <h3>Total Students</h3>
                <h2 class="text-primary"><?php echo $total_students; ?></h2>
            </div>
        </div>

        <!-- Total Courses Card -->
        <div class="col-md-6">
            <div class="card shadow-lg text-center p-4">
                <h3>Total Courses</h3>
                <h2 class="text-success"><?php echo $total_courses; ?></h2>
            </div>
        </div>
    </div>
</div>

</body>

</html>