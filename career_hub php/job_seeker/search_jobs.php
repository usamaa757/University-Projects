<?php
include 'header.php';
include "../db_connect.php";

// Get search parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';

// Base query
$sql = "SELECT * FROM jobs WHERE status='approved'";

// Filters (using prepared statements)
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (job_title LIKE ? OR company_name LIKE ?)";
    $searchParam = "%$search%";
    $params[] = &$searchParam;
    $params[] = &$searchParam;
    $types .= "ss";
}
if (!empty($category)) {
    $sql .= " AND job_type = ?";
    $params[] = &$category;
    $types .= "s";
}
if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $locationParam = "%$location%";
    $params[] = &$locationParam;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$current_date = date('Y-m-d');

?>

<div class="container mt-5 rounded border shadow">
    <div class="row justify-content-center">


        <div class="card-header text-center bg-dark text-white mb-2">
            <h3>Find Jobs</h3>
        </div>

        <div class="p-3">
            <form method="get" class="row g-2 text-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Job Title or Company"
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="Full-time" <?php echo $category === "Full-time" ? "selected" : ""; ?>>Full-time
                        </option>
                        <option value="Part-time" <?php echo $category === "Part-time" ? "selected" : ""; ?>>Part-time
                        </option>
                        <option value="Contract" <?php echo $category === "Contract" ? "selected" : ""; ?>>Contract
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="location" class="form-control" placeholder="Location"
                        value="<?php echo htmlspecialchars($location); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-dark w-100">Search</button>
                </div>
            </form>

            <div class="mt-4">
                <?php if ($result->num_rows > 0) { ?>
                <div class="row">
                    <?php while ($row = $result->fetch_assoc()) {
                            $job_id = $row['job_id'];
                            $jobseeker_id = $_SESSION['user_id'] ?? 0;

                            // Check if user has a job seeker profile
                            $hasProfile = false;
                            if ($jobseeker_id) {
                                $profileQuery = $conn->prepare("SELECT id FROM job_seeker_profile WHERE job_seeker_id = ?");
                                $profileQuery->bind_param("i", $jobseeker_id);
                                $profileQuery->execute();
                                $profileResult = $profileQuery->get_result();
                                if ($profileResult->num_rows > 0) {
                                    $hasProfile = true;
                                }
                                $profileQuery->close();
                            }

                            // Check if the user has already applied
                            $applied = false;
                            if ($jobseeker_id) {
                                $checkQuery = $conn->prepare("SELECT id FROM job_applications WHERE job_seeker_id = ? AND job_id = ?");
                                $checkQuery->bind_param("ii", $jobseeker_id, $job_id);
                                $checkQuery->execute();
                                $checkResult = $checkQuery->get_result();
                                if ($checkResult->num_rows > 0) {
                                    $applied = true;
                                }
                                $checkQuery->close();
                            }

                            if (isset($_SESSION['user_id'])) { ?>

                    <div class="col-md-4">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($row['job_title']); ?>
                                </h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?php echo htmlspecialchars($row['company_name']); ?>
                                </h6>
                                <p class="card-text">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <?php echo htmlspecialchars($row['location']); ?><br>
                                    <strong>Job Type:</strong> <?php echo htmlspecialchars($row['job_type']); ?>
                                </p>
                                <p class="card-text">
                                    <strong>Last Date:</strong>
                                    <?php echo htmlspecialchars($row['application_deadline']); ?>
                                </p>
                                <?php if ($current_date < $row['application_deadline']) { ?>

                                <?php if (!$hasProfile) { ?>
                                <div class="alert alert-danger">You must <a href="create_profile.php"
                                        class="text-danger">upload
                                        your resume</a> before applying.</div>
                                <?php } elseif ($applied) { ?>
                                <button class="btn btn-secondary" disabled>Already Applied</button>
                                <?php } else {
                                                ?>
                                <a href="apply_job.php?job_id=<?php echo $row['job_id']; ?>"
                                    class="btn btn-outline-success">Apply
                                    Now</a>
                                <?php }
                                            } else { ?>
                                <div class="text-danger">Date Expired</div>
                                <?php }
                                        } ?>

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
    </div>
</div>

</body>

</html>