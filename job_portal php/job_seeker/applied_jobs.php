<?php
include '../db.php';
include 'header.php';

$seeker_id = $_SESSION['user_id'];

// Fetch applied jobs
$sql = "SELECT j.title, j.location, j.salary, j.description, a.applied_at, a.cv_file 
        FROM job_applications a 
        JOIN jobs j ON a.job_id = j.job_id 
        WHERE a.seeker_id = $seeker_id";

$result = mysqli_query($conn, $sql);
?>

<div class="container">
    <h2>Your Applied Jobs</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="job">
        <h3><?php echo $row['title']; ?></h3>
        <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
        <p><strong>Salary:</strong> <?php echo $row['salary']; ?></p>
        <p><strong>Applied On:</strong> <?php echo date("d M Y", strtotime($row['applied_at'])); ?></p>
        <p><strong>CV:</strong> <a href="<?php echo $row['cv_file']; ?>" class="btn" target="_blank">View CV</a>
        </p>
        <p><?php echo nl2br($row['description']); ?></p>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <p>You have not applied for any jobs yet.</p>
    <?php endif; ?>
</div>