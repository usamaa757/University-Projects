<?php

include 'header.php';
include "../db_connection.php";


// Fetch pending users and sellers
$result = $conn->query("SELECT * FROM users");
?>

<div class="container mt-3">
    <a href="admin_dashboard.php"><button class="btn btn-primary mb-2">Back to Dashboard</button></a>
    <div class="border shadow-sm rounded">
        <h2 class="text-center bg-dark p-2 text-white">User Requests</h2>
        <div class="p-3">
            <?php
            if (isset($_GET['msg'])) {
                // Display success message
                echo '<div class="text-success" role="alert">' . htmlspecialchars($_GET['msg']) . '</div>';
            }

            if (isset($_GET['error'])) {
                // Display error message
                echo '<div class="text-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            <h3>users</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                                style="display:inline;">
                                <input type="hidden" name="user_id"
                                    value="<?php echo htmlspecialchars($row['user_id']); ?>">
                                <input type="hidden" name="user_type" value="users">
                                <input type="hidden" name="action" value="approve">
                                <a href="edit_user.php?user_id=<?php echo $row['user_id']; ?>"
                                    class="btn btn-warning btn-sm">Edit</a>

                            </form>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

</html>