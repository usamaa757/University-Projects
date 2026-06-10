<?php
include 'navbar.php';
require 'db.php';

// // Check if faculty is logged in
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
//     header('Location: login.php');
//     exit;
// }

$faculty_id = $_SESSION['user_id'];

// Fetch all submissions for this faculty's students
$submissions_result = mysqli_query($conn, "
    SELECT s.id AS submission_id,
           s.title AS paper_title,
           s.status,
           s.created_at,
           u.full_name AS student_name,
           u.student_id,
           a.title AS assignment_title,
           a.category
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    JOIN assignments a ON s.assignment_id = a.id
    WHERE s.selected_supervisor_id = $faculty_id
    ORDER BY s.created_at DESC
");

// Count submissions by status
$status_counts = [];
$status_result = mysqli_query($conn, "
    SELECT status, COUNT(*) AS count
    FROM submissions
    WHERE selected_supervisor_id = $faculty_id
    GROUP BY status
");
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_counts[$row['status']] = $row['count'];
}
?>

<div class="report-container">
    <h2>Faculty Report: My Students' Submissions</h2>

    <h3>Submission Summary</h3>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $all_statuses = ['Submitted', 'Accepted', 'Rejected', 'Needs Improvement', 'Accepted and Published'];
            foreach ($all_statuses as $status) {
                $count = isset($status_counts[$status]) ? $status_counts[$status] : 0;
                echo "<tr><td>{$status}</td><td>{$count}</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h3>Detailed Submissions</h3>
    <table class="details-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Paper Title</th>
                <th>Assignment</th>
                <th>Category</th>
                <th>Status</th>
                <th>Submitted On</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($sub = mysqli_fetch_assoc($submissions_result)): ?>
            <tr>
                <td><?= htmlspecialchars($sub['student_name']) ?></td>
                <td><?= htmlspecialchars($sub['student_id']) ?></td>
                <td><?= htmlspecialchars($sub['paper_title']) ?></td>
                <td><?= htmlspecialchars($sub['assignment_title']) ?></td>
                <td><?= htmlspecialchars($sub['category']) ?></td>
                <td><?= htmlspecialchars($sub['status']) ?></td>
                <td><?= date('d-m-Y', strtotime($sub['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<style>
.report-container {
    width: 95%;
    margin: 30px auto;
    font-family: Arial, sans-serif;
}

.report-container h2 {
    color: #003366;
    margin-bottom: 20px;
}

.summary-table,
.details-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

.summary-table th,
.summary-table td,
.details-table th,
.details-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.summary-table th,
.details-table th {
    background-color: #003366;
    color: white;
}

.summary-table td,
.details-table td {
    background: #f9f9f9;
}

.summary-table tbody tr:nth-child(even),
.details-table tbody tr:nth-child(even) {
    background: #f1f1f1;
}
</style>