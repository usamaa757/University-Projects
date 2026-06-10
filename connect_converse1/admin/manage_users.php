<?php
include '../db.php';
include 'header.php';

// Fetch users from the database
$query = "SELECT * FROM users WHERE status  = 'Pending'";
$result = $conn->query($query);


if (isset($_POST['Active']) || isset($_POST['Rejected'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['Active'];
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
}
if (isset($_POST['Rejected'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['Rejected'];
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
}
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Manage Users</h3>


    <div class="table table-bordered table-hover">
        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>User Name </th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>

                    <td>
                        <?php if ($row['status'] === 'Pending'): ?>
                        <span class="badge bg-warning">Pending</span>
                        <?php elseif ($row['status'] === 'Active'): ?>
                        <span class="badge bg-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td>


                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">

                            <button type="submit" name="Active" class="btn btn-sm btn-success btn-sm">Active</button>
                        </form>

                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">

                            <button type="submit" name="Rejected" class="btn btn-sm btn-danger btn-sm">Rejected</button>
                        </form>

                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>