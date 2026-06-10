<?php
include '../db.php';
include 'header.php';

$employer_id = $_SESSION['user_id']; // simple sanitization

// Delete job if requested
if (isset($_GET['delete'])) {
    $job_id = $_GET['delete']; // cast to int for safety

    $delete_query = "DELETE FROM jobs WHERE job_id = $job_id AND employer_id = $employer_id";
    mysqli_query($conn, $delete_query);

    echo "<script>alert('Job deleted successfully.'); window.location.href='job_list.php';</script>";
    exit;
}

// Fetch jobs with application count
$query = "SELECT j.*, 
            (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.job_id) AS total_applications
          FROM jobs j
          WHERE j.employer_id = $employer_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error fetching jobs: " . mysqli_error($conn);
    exit;
}

?>

<div class="container">


    <h2 style="display: flex; justify-content: space-between; align-items: center;">
        Your Posted Jobs
        <a href="post_job.php" class="btn">+ Post New Job</a>
    </h2>

    <section class="job-listings">

        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="job">
            <h3><?php echo $row['title']; ?></h3>
            <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
            <p><strong>Company:</strong> <?php echo $row['company']; ?></p>
            <p><strong>Salary:</strong> <?php echo $row['salary']; ?></p>
            <p><strong>Description:</strong> <?php echo nl2br($row['description']); ?></p>
            <p><strong>Total Applicants:</strong> <?php echo nl2br($row['total_applications']); ?></p>


            <div class="buttons">
                <a href="edit_job.php?job_id=<?php echo $row['job_id']; ?>" class="btn">Edit</a> |
                <a href="job_list.php?delete=<?php echo $row['job_id']; ?>" class="btn"
                    onclick="return confirm('Are you sure you want to delete this job?')">Delete</a> |
                <a href="view_applications.php?job_id=<?php echo $row['job_id']; ?>" class="btn">View Applicants</a>
            </div>
        </div>
        <?php endwhile; ?>



    </section>
</div>
</body>

</html>div