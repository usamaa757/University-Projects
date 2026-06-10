<?php
include 'navbar.php';
require 'db.php';

// Access control: only admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

/* -----------------------------------------------------------
   SUMMARY COUNTS
------------------------------------------------------------*/

// Total submissions
$total_submissions = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM submissions
"))['total'];

// Pending
$pending = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM submissions WHERE status='Pending'
"))['total'];

// Accepted
$accepted = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM submissions WHERE status='Accepted'
"))['total'];

// Rejected
$rejected = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM submissions WHERE status='Rejected'
"))['total'];

// Accepted & Published
$published = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM submissions WHERE status='Accepted and Published'
"))['total'];

/* -----------------------------------------------------------
   CATEGORY CHART DATA
------------------------------------------------------------*/

$category_query = mysqli_query($conn, "
    SELECT category, COUNT(*) AS total
    FROM assignments 
    JOIN submissions ON submissions.assignment_id = assignments.id
    GROUP BY category
");

$category_labels = [];
$category_values = [];

while ($row = mysqli_fetch_assoc($category_query)) {
    $category_labels[] = $row['category'];
    $category_values[] = $row['total'];
}

/* -----------------------------------------------------------
   PROGRAM CHART DATA
------------------------------------------------------------*/
$program_query = mysqli_query($conn, "
    SELECT u.program, COUNT(*) AS total
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    GROUP BY u.program
");

$program_labels = [];
$program_values = [];

while ($row = mysqli_fetch_assoc($program_query)) {
    $program_labels[] = $row['program'];
    $program_values[] = $row['total'];
}

/* -----------------------------------------------------------
   Evaluation Status Chart
------------------------------------------------------------*/
$status_query = mysqli_query($conn, "
    SELECT status, COUNT(*) AS total
    FROM submissions
    GROUP BY status
");

$status_labels = [];
$status_values = [];

while ($row = mysqli_fetch_assoc($status_query)) {
    $status_labels[] = $row['status'];
    $status_values[] = $row['total'];
}

/* -----------------------------------------------------------
   Published Papers List
------------------------------------------------------------*/

$published_list = mysqli_query($conn, "
    SELECT s.title, u.full_name, u.student_id, a.category, s.created_at
    FROM submissions s
    JOIN users u ON u.id = s.student_id
    JOIN assignments a ON a.id = s.assignment_id
    WHERE s.status='Accepted and Published'
    ORDER BY s.created_at DESC
");

?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="report-container">

    <h2>Admin Reports Dashboard</h2>

    <!-- Summary Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Submissions</h3>
            <p><?= $total_submissions ?></p>
        </div>
        <div class="card">
            <h3>Pending</h3>
            <p><?= $pending ?></p>
        </div>
        <div class="card">
            <h3>Accepted</h3>
            <p><?= $accepted ?></p>
        </div>
        <div class="card">
            <h3>Rejected</h3>
            <p><?= $rejected ?></p>
        </div>
        <div class="card">
            <h3>Published</h3>
            <p><?= $published ?></p>
        </div>
    </div>

    <!-- Charts -->
    <canvas id="categoryChart"></canvas>
    <canvas id="programChart" style="margin-top:20px;"></canvas>
    <canvas id="statusChart" class="statusChart" style="margin-top:20px; width:200px"></canvas>

    <script>
    // CATEGORY CHART
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($category_labels) ?>,
            datasets: [{
                label: "Submissions by Category",
                data: <?= json_encode($category_values) ?>
            }]
        }
    });

    // PROGRAM CHART
    new Chart(document.getElementById('programChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($program_labels) ?>,
            datasets: [{
                label: "Submissions by Program",
                data: <?= json_encode($program_values) ?>
            }]
        }
    });

    // STATUS PIE CHART
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($status_labels) ?>,
            datasets: [{
                data: <?= json_encode($status_values) ?>
            }]
        }
    });
    </script>

    <!-- Published Papers Table -->
    <h2>Published Research Papers</h2>

    <table>
        <tr>
            <th>Title</th>
            <th>Student</th>
            <th>Student ID</th>
            <th>Category</th>
            <th>Published On</th>
        </tr>

        <?php while ($p = mysqli_fetch_assoc($published_list)) : ?>
        <tr>
            <td><?= $p['title'] ?></td>
            <td><?= $p['full_name'] ?></td>
            <td><?= $p['student_id'] ?></td>
            <td><?= $p['category'] ?></td>
            <td><?= date("d-m-Y", strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>
</body>

</html>