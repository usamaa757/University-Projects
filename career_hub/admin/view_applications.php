<?php
include 'header.php';
include '../db_connect.php';

// Fetch job applications with job seeker details
$sql = "SELECT ja.*,
               j.*,
               js.*
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.job_id
        JOIN job_seeker_profile js ON ja.job_seeker_id = js.job_seeker_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5 rounded border shadow">
    <div class="row justify-content-center">


        <div class="card-header text-center bg-dark text-white mb-2">
            <h3>Job Applications</h3>
        </div>

        <div class="p-3">

            <?php if ($result->num_rows > 0) { ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Company</th>
                        <th>Job Title</th>
                        <th>Job Type</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['job_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['applied_at']); ?></td>
                        <td>
                            <span
                                class="badge 
                            <?php echo ($row['status'] === 'Pending') ? 'bg-warning' : (($row['status'] === 'Reviewed') ? 'bg-secondary' : 'bg-success'); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-outline-info btn-sm viewProfileBtn" data-bs-toggle="modal"
                                data-bs-target="#profileModal" data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                data-phone="<?php echo htmlspecialchars($row['contact']); ?>"
                                data-resume="<?php echo htmlspecialchars($row['resume']); ?>">
                                View Applicant
                            </button>
                        </td>
                    </tr>

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


<!-- Job Seeker Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Job Seeker Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="profileName"></span></p>
                <p><strong>Email:</strong> <span id="profileEmail"></span></p>
                <p><strong>Phone:</strong> <span id="profilePhone"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.viewProfileBtn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('profileName').textContent = this.getAttribute('data-name');
        document.getElementById('profileEmail').textContent = this.getAttribute('data-email');
        document.getElementById('profilePhone').textContent = this.getAttribute('data-phone');
    });
});
</script>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>