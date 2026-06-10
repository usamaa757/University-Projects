<?php
session_start();
include "db_connect.php";

// Redirect if not logged in or not an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

// Fetch employer details
$employer_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();
$employer = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h3>Employer Dashboard</h3>
                <h5>Welcome, <?php echo htmlspecialchars($employer['name']); ?>!</h5>
            </div>
            <?php
            if ($_SESSION['role'] != 'jobseeker') {
            }
            ?>
            <div class="card-body">
                <h4>Manage Your Jobs</h4>
                <a href="post_job.php" class="btn btn-success">Post a New Job</a>
                <a href="manage_jobs.php" class="btn btn-info">Manage Job Listings</a>
                <a href="view_applications.php" class="btn btn-warning">View Applications</a>

                <hr>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </div>

</body>

</html>