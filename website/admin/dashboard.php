<?php
include 'header.php';
include '../db_connection.php';
?>
<br><br><br><br><br>
<div class="container-fluid">
    <h1 class="display-4">Admin Dashboard</h1>

    <!-- Row 1: Summary Cards -->
    <div class="row my-4">
        <!-- Total Students -->
        <div class="col-md-3">
            <div class="card text-white bg-indigo mb-3">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Students</h5>
                    <p class="card-text">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM students");
                        $data = $result->fetch_assoc();
                        echo $data['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Subjects -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title text-white ">Total Subjects</h5>
                    <p class="card-text">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM subjects");
                        $data = $result->fetch_assoc();
                        echo $data['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Mid Exams -->
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Mid Exams</h5>
                    <p class="card-text">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM mid_exams WHERE exam_type = 'Mid'");
                        $data = $result->fetch_assoc();
                        echo $data['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Final Exams -->
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Final Exams</h5>
                    <p class="card-text">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM final_exams WHERE exam_type = 'Final'");
                        $data = $result->fetch_assoc();
                        echo $data['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Recent Activities and Announcements -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-cyan">
                    <h5 class="text-white">Recent Student Registrations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php
                        $result = $conn->query("SELECT name, registration_date FROM students ORDER BY registration_date DESC LIMIT 5");
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='list-group-item'>{$row['name']} - Registered on {$row['registration_date']}</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-teal">
                    <h5 class="text-white">Announcements</h5>
                </div>
                <div class="card-body">
                    <p>Upcoming exams, new policies, or announcements can go here.</p>
                    <button class="btn btn-primary btn-sm">View All</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Quick Links to Admin Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pink">
                    <h5 class="text-white">Quick Actions</h5>
                </div>
                <div class="card-body text-center">
                    <a href="students.php" class="btn btn-outline-primary m-2">Manage Students</a>
                    <a href="subjects.php" class="btn btn-outline-success m-2">Manage Subjects</a>
                    <a href="exams.php" class="btn btn-outline-warning m-2">Manage Exams</a>
                    <a href="upload_handouts.php" class="btn btn-outline-danger m-2">Manage Handouts</a>
                    <a href="upload_papers.php" class="btn btn-outline-secondary m-2">Manage Papers</a>
                </div>
            </div>
        </div>
    </div>
</div>