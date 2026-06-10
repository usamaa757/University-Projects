<?php
include 'header.php';
include "../db_connect.php";

if ($_SESSION['role'] != 'employer') {
    die("Unauthorized Access!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $requirements = $_POST['requirements'];
    $salary = $_POST['salary'];
    $job_type = $_POST['job_type'];
    $deadline = $_POST['deadline'];
    $employer_id = $_SESSION['user_id'];

    $sql = "INSERT INTO jobs (employer_id, job_title, company_name, location, job_requirements, salary_range, job_type, application_deadline, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $employer_id, $title, $company, $location, $requirements, $salary, $job_type, $deadline);

    if ($stmt->execute()) {
        echo "<script>alert('Job Posted Successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h3>Post a Job</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label>Job Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Company Name</label>
                            <input type="text" name="company" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Job Requirements</label>
                            <textarea name="requirements" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Salary Range</label>
                            <input type="text" name="salary" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Job Type</label>
                            <select name="job_type" class="form-select">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Application Deadline</label>
                            <input type="date" name="deadline" class="form-control" required>
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn btn-outline-dark">Post Job</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>