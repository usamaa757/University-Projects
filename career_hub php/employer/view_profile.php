<?php
include 'header.php';
include '../db_connect.php';

if (!isset($_GET['user_id'])) {
    echo "<div class='alert alert-danger'>Invalid request!</div>";
    exit;
}

$job_seeker_id = $_GET['user_id'];

// Fetch job seeker profile details
$sql = "SELECT * FROM job_seeker_profile WHERE job_seeker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_seeker_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    echo "<div class='alert alert-warning'>Profile not found.</div>";
    exit;
}
?>

<div class="container mt-4 border round shadow p-3">
    <h2 class="text-center">Job Seeker Profile</h2>

    <table class="table table">
        <tr>
            <th>Name:</th>
            <td><?php echo htmlspecialchars($profile['name']); ?></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><?php echo htmlspecialchars($profile['email']); ?></td>
        </tr>
        <tr>
            <th>Phone:</th>
            <td><?php echo htmlspecialchars($profile['contact']); ?></td>
        </tr>
        <tr>
            <th>Education:</th>
            <td><?php echo htmlspecialchars($profile['education']); ?></td>
        </tr>
        <tr>
            <th>Experience:</th>
            <td><?php echo htmlspecialchars($profile['experience']); ?> years</td>
        </tr>
        <tr>
            <th>Resume:</th>
            <td>
                <?php if (!empty($profile['resume'])) { ?>
                <a href="<?php echo $base_url . 'job_seeker/' . htmlspecialchars($profile['resume']); ?>"
                    target="_blank" class="btn btn-primary btn-sm">Download Resume</a>
                <?php } else { ?>
                <span class="text-danger">No Resume Uploaded</span>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>