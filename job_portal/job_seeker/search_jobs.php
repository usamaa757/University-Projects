<?php
require '../db.php';
include 'header.php';

$keyword = $_GET['keyword'] ?? '';
$location = $_GET['location'] ?? '';
$min_salary = $_GET['min_salary'] ?? '';

// Build SQL query dynamically
$sql = "SELECT * FROM jobs WHERE 1";

if (!empty($keyword)) {
    $sql .= " AND (title LIKE '%$keyword%' OR description LIKE '%$keyword%')";
}

if (!empty($location)) {
    $sql .= " AND location LIKE '%$location%'";
}

if (!empty($min_salary)) {
    $sql .= " AND salary >= $min_salary";
}

$result = mysqli_query($conn, $sql);
?>

<div class="container">

    <div class="search-form">
        <form method="get">
            <input type="text" name="keyword" placeholder="Keyword (e.g. Developer)" value="<?php echo $keyword; ?>">
            <input type="text" name="location" placeholder="Location" value="<?php echo $location; ?>">
            <input type="number" name="min_salary" placeholder="Min Salary" value="<?php echo $min_salary; ?>">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>


    <section class="job-listings">
        <h2>Latest Job Openings</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="job">
            <h3><?php echo $row['title']; ?></h3>
            <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
            <p><strong>Company:</strong> <?php echo $row['company']; ?></p>
            <p><strong>Salary:</strong> <?php echo $row['salary']; ?></p>
            <p><strong>Desciption:</strong><?php echo nl2br(string: $row['description']); ?></p>

            <?php
                    $seeker_id = $_SESSION['user_id'];
                    $job_id = $row['job_id'];

                    $check_sql = "SELECT * FROM job_applications WHERE job_id = $job_id AND seeker_id = $seeker_id";
                    $check_result = mysqli_query($conn, $check_sql);
                    if (mysqli_num_rows($check_result) > 0): ?>
            <button class="btn" disabled>Already Applied</button>
            <?php else: ?>
            <form method="post" action="apply_job.php" enctype="multipart/form-data">
                <input type="hidden" name="job_id" value="<?php echo $row['job_id']; ?>">
                <label>Upload Your CV (PDF or DOC):</label><br>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" required><br><br>
                <button type="submit" class="btn">Submit Application</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>


        <?php else: ?>
        <p>No jobs found matching your criteria.</p>
        <?php endif; ?>
    </section>
</div>
</body>

</html>