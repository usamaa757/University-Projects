<?php
include 'navbar.php';
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// ---------------------
// Submission statistics
// Total submissions
$total_submissions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM submissions"))['total'];

// Pending evaluations
$pending_evaluations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS pending FROM submissions WHERE status='Submitted' OR status='Needs Improvement'"))['pending'];

// Accepted and Published papers
$published_papers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS published FROM submissions WHERE status='Accepted and Published'"))['published'];

// Fetch all users with role names
$users_result = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' AND is_active ='0' ORDER BY created_at DESC LIMIT 3");


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}


// Handle approve, reject, delete actions
if (isset($_GET['approve_user'])) {
    $user_id = intval($_GET['approve_user']);
    $sql = "UPDATE users SET is_active = 1 WHERE id = $user_id";
    mysqli_query($conn, $sql);
    header('Location: admin_dashboard.php');
    exit;
}

if (isset($_GET['reject_user'])) {
    $user_id = intval($_GET['reject_user']);
    $sql = "DELETE FROM users WHERE id = $user_id"; // Or set is_active=0 for soft reject
    mysqli_query($conn, $sql);
    header('Location: admin_dashboard.php');
    exit;
}

if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $sql = "DELETE FROM users WHERE id = $user_id";
    mysqli_query($conn, $sql);
    header('Location: admin_dashboard.php');
    exit;
}
?>
<div class="dashboard-container">
    <h2>Admin Dashboard</h2>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card">
            <h3>Total Submissions</h3>
            <p><?php echo $total_submissions; ?></p>
        </div>
        <div class="stat-card">
            <h3>Pending Evaluations</h3>
            <p><?php echo $pending_evaluations; ?></p>
        </div>
        <div class="stat-card">
            <h3>Published Papers</h3>
            <p><?php echo $published_papers; ?></p>
        </div>
    </div>

    <!-- Users Table -->
    <h3>Registered Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Student ID</th>
                <th>Program</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['full_name']; ?></td>
                <td><?php echo $user['university_email']; ?></td>
                <td><?php echo $user['role']; ?></td>
                <td><?php echo $user['student_id']; ?></td>
                <td><?php echo $user['program']; ?></td>
                <td><?php echo $user['is_active'] ? 'Active' : 'Pending'; ?></td>
                <td>
                    <?php if (!$user['is_active']) { ?>
                    <a class="btn btn-green"
                        href="admin_dashboard.php?approve_user=<?php echo $user['id']; ?>">Approve</a>
                    <a class="btn btn-red" href="admin_dashboard.php?reject_user=<?php echo $user['id']; ?>">Reject</a>
                    <?php } ?>
                    <a class="btn btn-red" href="admin_dashboard.php?delete_user=<?php echo $user['id']; ?>">Delete</a>
                </td>

            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>

</html>