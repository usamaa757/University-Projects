<?php
include 'db.php';
include 'header.php';

// Fetch counts
$totalStudents = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$totalCourses = $conn->query("SELECT COUNT(*) AS count FROM courses")->fetch_assoc()['count'];
$totalSeats = $conn->query("SELECT COUNT(*) AS count FROM seating_arrangements")->fetch_assoc()['count'];
?>

<div class="container mt-5">
    <h2 class="mb-4">📊 Admin Dashboard</h2>

    <!-- Dashboard Stats -->
    <div class="row text-white">
        <div class="col-md-4">
            <div class="card bg-primary mb-3">
                <div class="card-body">
                    <h5>Total Students</h5>
                    <h3><?= $totalStudents ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success mb-3">
                <div class="card-body">
                    <h5>Total Courses</h5>
                    <h3><?= $totalCourses ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning mb-3">
                <div class="card-body">
                    <h5>Total Seats Assigned</h5>
                    <h3><?= $totalSeats ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4">
        <h4>Quick Actions</h4>
        <a href="view_seating.php" class="btn btn-outline-primary me-2">📋 View Seating</a>
        <a href="export_pdf.php" class="btn btn-outline-secondary me-2">🖨️ Export PDF</a>
        <a href="send_msg.php" class="btn btn-outline-success">📧 Send Notifications</a>
    </div>
</div>