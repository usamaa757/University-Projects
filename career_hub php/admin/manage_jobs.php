<?php
include 'header.php';
include "../db_connect.php";


// Handle Approve, Reject, and Delete Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $job_id = $_POST['job_id'];
        $conn->query("UPDATE jobs SET status='approved' WHERE job_id=$job_id");
    } elseif (isset($_POST['reject'])) {
        $job_id = $_POST['job_id'];
        $conn->query("UPDATE jobs SET status='rejected' WHERE job_id=$job_id");
    } elseif (isset($_POST['delete'])) {
        $job_id = $_POST['job_id'];
        $conn->query("DELETE FROM jobs WHERE job_id=$job_id");
    }
}

// Fetch all jobs (approved & pending)
$sql = "SELECT * FROM jobs ORDER BY status ASC, job_id DESC";
$result = $conn->query($sql);
?>


<div class="container mt-5 rounded border shadow">
    <div class="row justify-content-center">


        <div class="card-header text-center bg-dark text-white mb-2">
            <h3>Manage Jobs</h3>
        </div>

        <div class="p-3">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Job Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) {
                        $count = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['job_type']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'approved') { ?>
                            <span class="badge bg-success">Approved</span>
                            <?php } elseif ($row['status'] == 'rejected') { ?>
                            <span class="badge bg-danger">Rejected</span>
                            <?php } else { ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php } ?>
                        </td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="job_id" value="<?php echo $row['job_id']; ?>">
                                <?php if ($row['status'] == 'pending') { ?>
                                <button type="submit" name="approve"
                                    class="btn btn-sm btn-outine-success">Approve</button>
                                <button type="submit" name="reject"
                                    class="btn btn-sm btn-outline-warning">Reject</button>
                                <?php } ?>
                                <button type="submit" name="delete" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php }
                    } else { ?>
                    <tr>
                        <td colspan="7" class="text-center">No job listings found.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>

</html>