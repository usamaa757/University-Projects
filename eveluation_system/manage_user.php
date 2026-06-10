<?php
include 'navbar.php';
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all users with role names
$users_result = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at
DESC");


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
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
    <h2>Manage User</h2>

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

                    <a class="btn btn-red" href="admin_dashboard.php?delete_user=<?php echo $user['id']; ?>">Delete</a>
                </td>

            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>