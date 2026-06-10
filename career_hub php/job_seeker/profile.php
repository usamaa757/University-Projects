<?php
include 'header.php';
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$jobseeker_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT * FROM job_seeker_profile WHERE job_seeker_id = ?");
$query->bind_param("i", $jobseeker_id);
$query->execute();
$result = $query->get_result();
$profile = $result->fetch_assoc();

?>

<div class="container mt-5 rounded border shadow">
    <div class="row justify-content-center">


        <div class="card-header text-center bg-dark text-white mb-2">
            <h3>Job Seeker Profile</h3>
        </div>

        <div class="card-body p-3">
            <?php
            if ($profile) { ?>
            <table class="table">
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($profile['name']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($profile['email']); ?></td>
                </tr>
                <tr>
                    <th>Contact:</th>
                    <td><?php echo htmlspecialchars($profile['contact']); ?></td>
                </tr>
                <tr>
                    <th>Location:</th>
                    <td><?php echo htmlspecialchars($profile['location']); ?></td>
                </tr>
                <tr>
                    <th>Education:</th>
                    <td><?php echo nl2br(htmlspecialchars($profile['education'])); ?></td>
                </tr>
                <tr>
                    <th>Experience:</th>
                    <td><?php echo nl2br(htmlspecialchars($profile['experience'])); ?></td>
                </tr>
                <tr>
                    <th>Resume:</th>
                    <td>
                        <?php if (!empty($profile['resume'])) { ?>
                        <a href="<?php echo $profile['resume']; ?>" target="_blank"
                            class="btn btn-outline-dark">Download
                            Resume</a>
                        <?php } else {
                                echo "No resume uploaded.";
                            } ?>
                    </td>
                </tr>
            </table>
            <?php } else { ?>
            <div class="alert alert-warning">No profile found.</div>
            <?php } ?>
        </div>
    </div>
</div>


</body>

</html>