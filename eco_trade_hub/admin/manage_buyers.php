<?php
include("admin_header.php");

include("../db_connection.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all users from the database
$sql = "SELECT * FROM buyers";
$result = $conn->query($sql);
?>

<div class="container mt-3">
    <a href="manage_users.php"><button class="btn btn-primary mb-2">Back to Users</button></a>
    <div class="border shadow-sm rounded">
        <h2 class="text-center bg-dark p-2 text-white">Manage Users</h2>
        <table class="table table-bordered">
            
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    
                    <tr>
                        <td><?php echo htmlspecialchars($row['buyer_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        
                        <td>
                            <a href="edit_buyer.php?buyer_id=<?php echo $row['buyer_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <!-- <a href="delete_user.php?buyer_id=<?php echo $row['buyer_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a> -->
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

    </body>

    </html>