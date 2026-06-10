<?php
include 'header.php';
include '../db_connect.php';


$job_seeker_id = $_SESSION['user_id'];

// Fetch job applications for the employer's jobs
$sql = "SELECT ja.status, ja.job_id, ja.applied_at,
        j.job_title, j.company_name, j.job_type, j.salary_range, j.location
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.job_id
        WHERE ja.job_seeker_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_seeker_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="p-3">


    <div class="container mt-5 rounded border shadow">
        <div class="row justify-content-center">


            <div class="card-header text-center bg-dark text-white mb-2">
                <h3>Job Applications</h3>
            </div>

            <div class="p-3">
                <?php if ($result->num_rows > 0) { ?>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Company Name</th>
                            <th>Job Title</th>
                            <th>Location</th>
                            <th>Job Type</th>
                            <th>Salary</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['job_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['salary_range']); ?></td>
                            <td><?php echo htmlspecialchars($row['applied_at']); ?></td>


                            <td>

                                <?php if ($row['status'] === 'Pending') { ?>

                                <span class="badge bg-warning"><?php echo htmlspecialchars($row['status']); ?></span>

                                <?php } elseif ($row['status'] === 'Reviewed') { ?>

                                <span class="badge bg-secondary"><?php echo htmlspecialchars($row['status']); ?></span>
                                <?php } elseif ($row['status'] === 'Approved') { ?>
                                <span class="badge bg-success"><?php echo htmlspecialchars($row['status']); ?></span>
                                <?php } else { ?>
                                <span class="badge bg-danger"><?php echo htmlspecialchars($row['status']); ?></span>
                                <?php } ?>


                            </td>

                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="alert alert-warning">No applications found.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>