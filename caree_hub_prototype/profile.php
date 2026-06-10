<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$jobseeker_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT * FROM job_seekers WHERE job_seeker_id = ?");
$query->bind_param("i", $jobseeker_id);
$query->execute();
$result = $query->get_result();
$profile = $result->fetch_assoc();
?>

<div class="container mt-5">
    <h2>Job Seeker Profile</h2>
    <table class="table table-bordered">
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
                    <a href="<?php echo $profile['resume']; ?>" target="_blank" class="btn btn-primary">Download
                        Resume</a>
                <?php } else {
                    echo "No resume uploaded.";
                } ?>
            </td>
        </tr>
    </table>
</div>
</body>

</html>