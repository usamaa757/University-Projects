<?php
include 'header.php';
include '../db_connect.php';


$employer_id = $_SESSION['user_id'];

// Fetch job applications for the employer's jobs
$sql = "SELECT * FROM jobs WHERE employer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();



//delete job
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    $stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();

    header("Location: job_list.php");
    exit();
}

?>


<div class="container mt-5 rounded border shadow">
    <div class="row justify-content-center">


        <div class="card-header text-center bg-dark text-white mb-2">
            <h3>Jobs List</h3>
        </div>

        <div class="p-3">
            <?php if ($result->num_rows > 0) { ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Job Title</th>
                        <th>Company Name</th>
                        <th>Job Type</th>
                        <th>Dead Line</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['job_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['application_deadline']); ?></td>



                        <td>
                            <?php if ($row['status'] === 'pending') { ?>
                            <span class="badge bg-warning"><?php echo htmlspecialchars($row['status']); ?></span>

                            <?php } else { ?>
                            <span class="badge bg-success"><?php echo htmlspecialchars($row['status']); ?></span>

                            <?php } ?>
                        </td>
                        <td>
                            <a href="edit_job.php?job_id=<?php echo $row['job_id']; ?>" class="btn btn-info btn-sm">Edit
                            </a>
                            <a href="job_list.php?job_id=<?php echo $row['job_id']; ?>"
                                class="btn btn-danger btn-sm">Delete
                            </a>
                        </td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div class="alert alert-warning">No job found.</div>
            <?php } ?>
        </div>
    </div>
</div>

</body>

</html>

<?php
$stmt->close();
$conn->close();