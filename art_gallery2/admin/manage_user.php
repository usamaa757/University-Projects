<?php include '../db.php';
include 'header.php';

if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    $sql = "UPDATE users SET status='approved' WHERE user_id=$user_id";
    if ($conn->query($sql)) {
        echo "<script>alert('User approved successfully!'); window.location='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
} elseif (isset($_GET['reject'])) {
    $user_id = $_GET['reject'];
    $sql = "UPDATE users SET status='rejected' WHERE user_id=$user_id";
    if ($conn->query($sql)) {
        echo "<script>alert('User rejected successfully!'); window.location='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
$result = $conn->query("SELECT * FROM users WHERE status='pending'");


?>


<div class="container mt-5 border rounded shadow">
    <h3 class="text-center">Pending User Approvals</h3>
    <table class="table table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

            <?php

            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td>
                    <a href="?approve=<?php echo $row['user_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="?reject=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
            <?php endwhile;
            if ($result->num_rows == 0) {
                echo "<tr><td colspan='4' class = 'text-center'> No pending users found</td></td>";
            } ?>
        </tbody>
    </table>
</div>