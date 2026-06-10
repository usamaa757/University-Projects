<?php
include "../db.php";
include 'header.php';


$job_id = $_GET['job_id'];

if (!isset($_GET['job_id'])) {
    echo "<script>alert('Invalid job ID'); window.location='employer_jobs.php';</script>";
    exit;
}

$jobQuery = "SELECT title FROM jobs WHERE job_id = $job_id";
$jobResult = mysqli_query($conn, $jobQuery);

if ($jobResult && mysqli_num_rows($jobResult) > 0) {
    $row = mysqli_fetch_assoc($jobResult);
    $jobTitle = $row['title'];
}

// Fetch applicants
$query = "SELECT ja.cv_file, js.name, js.email, js.phone
          FROM job_applications ja
          JOIN job_seekers js ON ja.seeker_id = js.seeker_id
          WHERE ja.job_id = $job_id";

$result = mysqli_query($conn, $query);
?>


<h2>Applicants for: <?php echo $jobTitle; ?></h2>

<?php if ($result->num_rows > 0): ?>
<table border="1" cellpadding="8">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>CV</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><a href="../job_seeker/<?php echo $row['cv_file']; ?>" target="_blank">Download CV</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>No applicants yet.</p>
<?php endif; ?>