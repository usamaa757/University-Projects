<?php
include 'header.php';
include '../db_connect.php';

$employer_id = $_SESSION['user_id'];

// Fetch job applications for the employer's jobs
$sql = "SELECT ja.status, ja.id AS application_id, ja.applied_at,  
               u.name, u.user_id,
               j.job_title, j.employer_id, j.job_id, j.job_type, j.salary_range, j.location
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.job_id
        JOIN users u ON ja.job_seeker_id = u.user_id
        WHERE j.employer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employer_id);
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
            <table class="table table">
                <thead class="table-dark">
                    <tr>
                        <th>Job Title</th>
                        <th>Job Seeker</th>
                        <th>Application Date</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['applied_at']); ?></td>
                        <td>

                            <a href="view_profile.php?user_id=<?php echo $row['user_id']; ?>"
                                class="btn btn-info btn-sm">View
                                Profile</a>
                        </td>



                        <td><?php echo htmlspecialchars(string: $row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] === 'Pending' || $row['status'] === 'Reviewed') { ?>
                            <form method="POST" action="process_application.php" style="display:inline;">
                                <input type="hidden" name="application_id"
                                    value="<?php echo $row['application_id']; ?>">
                                <button type="submit" name="review" class="btn btn-warning btn-sm">Reviewed</button>
                            </form>

                            <form method="POST" action="process_application.php" style="display:inline;">
                                <input type="hidden" name="application_id"
                                    value="<?php echo $row['application_id']; ?>">
                                <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                            </form>


                            <form method="POST" action="process_application.php" style="display:inline;">
                                <input type="hidden" name="application_id"
                                    value="<?php echo $row['application_id']; ?>">
                                <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                            </form>

                            <?php } else {
                                        if ($row['status'] === 'Pending') { ?>

                            <span class="badge bg-warning"><?php echo htmlspecialchars($row['status']); ?></span>

                            <?php } elseif ($row['status'] === 'Reviewed') { ?>

                            <span class="badge bg-secondary"><?php echo htmlspecialchars($row['status']); ?></span>
                            <?php } else { ?>
                            <span class="badge bg-success"><?php echo htmlspecialchars($row['status']); ?></span>
                            <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Profile View Modal (Move it outside the loop) -->
            <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="profileModalLabel">Job Seeker Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="profileContent">
                            <!-- Profile content will be loaded here via AJAdiv-->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php } else { ?>
            <div class="alert alert-warning">No applications found.</div>
            <?php } ?>

        </div>
    </div>

    </body>

    </html>

    <?php
    $stmt->close();
    $conn->close();
    ?>