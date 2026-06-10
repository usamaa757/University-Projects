<?php
include 'header.php';
include "../db_connect.php";



// Add User
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
    echo "<script>alert('User added successfully!'); window.location.href='manage_users.php';</script>";
    $stmt->close();
    exit();
}


if (isset($_POST['suspend_user'])) {
    $user_id = $_POST['user_id'];

    // Fetch current status from the database
    $stmt = $conn->prepare("SELECT status FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    // Toggle status
    $new_status = ($current_status == 'active') ? 'suspended' : 'active';

    // Update status in the database
    $stmt = $conn->prepare("UPDATE users SET status=? WHERE user_id=?");
    $stmt->bind_param("si", $new_status, $user_id);
    $stmt->execute();
    echo "<script>alert('User status updated successfully'); window.location.href='manage_users.php';</script>";
    $stmt->close();
    exit();
}


// Delete User
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    if ($role == 'job_seeker') {
        $stmt = $conn->prepare("DELETE FROM job_applications WHERE job_seeker_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM job_seeker_profile WHERE job_seeker_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    if ($role == 'employer') {
        $stmt = $conn->prepare("DELETE FROM jobs WHERE employer_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo "<script>alert('User deleted successfully'); window.location.href='manage_users.php';</script>";
    $stmt->close();
    exit();
}



// Fetch all users
$sql = "SELECT * FROM users WHERE role != 'admin'";
$result = $conn->query($sql);
?>

<div class="container mt-5 rounded border shadow">
    <div class="row justify-content-center">


        <div class="card-header text-center bg-dark text-white mb-2">
            <h3>Manage Users</h3>
        </div>


        <div class="col-md-5 container mt-3 rounded border shadow">

            <div class="row justify-content-center">


                <div class="card-header text-center bg-dark text-white mb-2">
                    <h3>Add New User</h3>
                </div>



                <!-- Add User Form -->
                <form method="post" class="d-inline">

                    <div class="mb-2">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="mb-2">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-2">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="mb-2">
                        <select name="role" class="form-select">
                            <option value="job_seeker">Job Seeker</option>
                            <option value="employer">Employer</option>
                        </select>
                    </div>
                    <div class="text-center my-3">

                        <button type="submit" name="add_user" class="btn btn-outline-dark">Add User</button>
                    </div>
                </form>

            </div>
        </div>


        <div class="p-3">

            <!-- User List -->
            <table class="table mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) {
                        $count = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'active') { ?>
                            <span class="badge bg-success">Active</span>
                            <?php } else { ?>
                            <span class="badge bg-danger">Suspended</span>
                            <?php } ?>
                        </td>
                        <td>

                            <a href="edit_user.php?user_id=<?php echo $row['user_id']; ?>"
                                class="btn btn-sm btn-outline-info">Edit
                            </a>


                            <!-- Suspend User -->
                            <form method="post" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">

                                <?php if ($row['status'] == 'active') { ?>
                                <button type="submit" name="suspend_user"
                                    class="btn btn-sm btn-outline-warning">Suspend</button>
                                <?php } else { ?>
                                <button type="submit" name="suspend_user"
                                    class="btn btn-sm btn-outline-success">Active</button>

                                <?php } ?>
                            </form>

                            <!-- Delete User -->
                            <form method="post" class="d-inline">
                                <input type="hidden" name="role" value="<?php echo $row['role']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <?php }
                    } else { ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>