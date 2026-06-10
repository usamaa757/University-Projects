<?php
include '../db.php';
include 'header.php';


$employer_id = $_SESSION['user_id'];

// Fetch employer details
$sql = "SELECT name, email, phone FROM employers WHERE employer_id = $employer_id";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
} else {
    echo "Employer not found.";
}
?>


<div class="dashboard-container">
    <h2>Welcome, <?php echo $name; ?></h2>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Phone:</strong> <?php echo $phone; ?></p>

    <h3>Dashboard Options</h3>
    <ul>
        <li><a href="post_job.php">Post a Job</a></li>
        <li><a href="job_list.php">Manage Posted Jobs</a></li>
        <li><a href="../logout.php" class="logout-btn">Logout</a></li>
    </ul>
</div>
</body>

</html>