<?php


include '../db.php';
include 'header.php';
// You can fetch job seeker specific data using user_id
$seeker_id = $_SESSION['user_id'];
$sql = "SELECT name, email, phone FROM job_seekers WHERE seeker_id = $seeker_id";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
} else {
    echo "Employer not found.";
}


$last_check_result = mysqli_query($conn, "SELECT last_checked FROM job_seekers WHERE seeker_id = $seeker_id");
$last_check_row = mysqli_fetch_assoc($last_check_result);
$last_checked = $last_check_row['last_checked'];

// Step 2: Find new jobs posted after last_checked
$new_jobs_query = "SELECT * FROM jobs WHERE created_at > '$last_checked' ORDER BY created_at DESC";
$new_jobs_result = mysqli_query($conn, $new_jobs_query);


?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo $name; ?></h2>
    <p>Email: <?php echo $email; ?></p>
    <p>Phone: <?php echo $phone; ?></p>

    <h3>Dashboard Options</h3>
    <ul>
        <li><a href="search_jobs.php">Search Jobs</a></li>
        <li><a href="applied_jobs.php">View Applied Jobs</a></li>
        <li><a href="../logout.php" class="logout-btn">Logout</a></li>
    </ul>
    <?php

    if (mysqli_num_rows($new_jobs_result) > 0): ?>
    <div class="notification-box">
        <h3>📢 New Jobs Posted Since Your Last Visit:</h3>
        <?php while ($job = mysqli_fetch_assoc($new_jobs_result)): ?>
        <div class="job">
            <strong><?php echo $job['title']; ?></strong> –
            <?php echo $job['location']; ?> |
            <?php echo $job['salary']; ?> <br>
            <small>Posted on: <?php echo date("d M Y", strtotime($job['created_at'])); ?></small>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <p>No new job postings since your last visit.</p>
    <?php endif;
    $now = date('Y-m-d H:i:s');
    mysqli_query($conn, "UPDATE job_seekers SET last_checked = '$now' WHERE seeker_id = $seeker_id");
    ?>

</div>
</body>

</html>