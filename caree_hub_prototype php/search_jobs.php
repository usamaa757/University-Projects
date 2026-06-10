<?php
include 'header.php';
include "db_connect.php";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

$sql = "SELECT * FROM jobs WHERE status='approved'";
if ($search) {
    $sql .= " AND (job_title LIKE '%$search%' OR company_name LIKE '%$search%')";
}
if ($category) {
    $sql .= " AND job_type = '$category'";
}
if ($location) {
    $sql .= " AND location LIKE '%$location%'";
}

$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2 class="text-center">Find Your Dream Job</h2>
    <form method="get" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Job Title or Company">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Contract">Contract</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="location" class="form-control" placeholder="Location">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <div class="mt-4">
        <?php if ($result->num_rows > 0) { ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()) {
                    $job_id = $row['job_id'];
                    $jobseeker_id = $_SESSION['user_id'] ?? 0;

                    // Check if user has applied for this job
                    $applied = false;
                    if ($jobseeker_id) {
                        $checkQuery = $conn->prepare("SELECT * FROM job_applications WHERE job_id = ? AND jobseeker_id = ?");
                        $checkQuery->bind_param("ii", $job_id, $jobseeker_id);
                        $checkQuery->execute();
                        $checkResult = $checkQuery->get_result();
                        if ($checkResult->num_rows > 0) {
                            $applied = true;
                        }
                    }
                ?>
                    <div class="col-md-4">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($row['job_title']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['company_name']); ?>
                                </h6>
                                <p class="card-text">
                                    <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($row['location']); ?>
                                    <br>
                                    <strong>Job Type:</strong> <?php echo htmlspecialchars($row['job_type']); ?>
                                </p>

                                <?php if ($applied) { ?>
                                    <button class="btn btn-secondary" disabled>Already Applied</button>
                                <?php } else { ?>
                                    <a href="apply_job.php?job_id=<?php echo $row['job_id']; ?>" class="btn btn-success">Apply
                                        Now</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center">No jobs found. Try different keywords.</div>
        <?php } ?>
    </div>
</div>
</body>

</html>