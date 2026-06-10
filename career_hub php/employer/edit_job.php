<?php
include 'header.php';
include '../db_connect.php';

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Fetch job details
    $sql = "SELECT * FROM jobs WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        echo "<script>alert('Job not found!'); window.location.href='manage_jobs.php';</script>";
        exit();
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='manage_jobs.php';</script>";
    exit();
}
?>

<div class="container mt-5 border rounded shadow p-3" style="max-width: 600px;">
    <h3 class="text-center">Edit Job details</h3>
    <form method="POST" action="update_job.php">
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['job_id']); ?>">

        <div class="mb-3">
            <label class="form-label">Job Title</label>
            <input type="text" name="job_title" class="form-control"
                value="<?php echo htmlspecialchars($job['job_title']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control"
                value="<?php echo htmlspecialchars($job['company_name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control"
                value="<?php echo htmlspecialchars($job['location']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Job Type</label>
            <select name="job_type" class="form-control" required>
                <option value="Full-Time" <?php echo ($job['job_type'] == 'Full-Time') ? 'selected' : ''; ?>>Full-Time
                </option>
                <option value="Part-Time" <?php echo ($job['job_type'] == 'Part-Time') ? 'selected' : ''; ?>>Part-Time
                </option>
                <option value="Contract" <?php echo ($job['job_type'] == 'Contract') ? 'selected' : ''; ?>>Contract
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Salary</label>
            <input type="text" name="salary_range" class="form-control"
                value="<?php echo htmlspecialchars($job['salary_range']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deadline</label>
            <input type="date" name="application_deadline" class="form-control"
                value="<?php echo htmlspecialchars($job['application_deadline']); ?>" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary ">Update Job</button>
            <a href="manage_jobs.php" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>

</body>

</html>