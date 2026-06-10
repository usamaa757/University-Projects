<?php
include 'header.php';
include "db_connect.php";

if ($_SESSION['role'] != 'admin') {
    die("Unauthorized Access!");
}

if (isset($_GET['approve'])) {
    $job_id = $_GET['approve'];
    $conn->query("UPDATE jobs SET status='approved' WHERE job_id=$job_id");
}

$pending_jobs = $conn->query("SELECT * FROM jobs WHERE status='pending'");
?>

<h2>Pending Job Approvals</h2>
<ul>
    <?php while ($job = $pending_jobs->fetch_assoc()) { ?>
        <li><?php echo $job['title']; ?> at <?php echo $job['company_name']; ?>
            <a href="?approve=<?php echo $job['job_id']; ?>">Approve</a>
        </li>
    <?php } ?>
</ul>