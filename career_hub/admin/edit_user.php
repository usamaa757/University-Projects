<?php
include 'header.php';
include "../db_connect.php";


// Check if user_id is provided in the URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Redirect if user not found
// if (!$user) {
//     echo "<script>alert('User ID is not provided!'); window.location.href='manag_users.php';</script>";
//     exit();
// }
// Handle form submission
if (isset($_POST['update_user'])) {


    $user_id = $_POST['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate password confirmation
    if (!empty($password) && $password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='edit_user.php?user_id=$user_id';</script>";
        exit();
    }

    // If password is provided, update it
    if (!empty($password)) {
        $hash_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hash_password, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Password updated successfully'); window.location.href='edit_user.php?user_id=$user_id';</script>";
        } else {
            echo "<script>alert('Failed to update password'); window.location.href='edit_user.php?user_id=$user_id';</script>";
        }
        $stmt->close();
    }

    // Update other user details
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $name, $email, $role, $status, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully'); window.location.href='edit_user.php?user_id=$user_id';</script>";
    } else {
        echo "<script>alert('Failed to update user'); window.location.href='edit_user.php?user_id=$user_id';</script>";
    }
    $stmt->close();

    $conn->close();
    exit();
}

?>

<div class="container mt-5 round border shadow p-3" style="max-width: 600px;">
    <h3 class="text-center">Edit User</h3>

    <form method="post" class="bg-white p-4">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>"
                required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="job_seeker" <?php echo ($user['role'] == 'job_seeker') ? 'selected' : ''; ?>>Job Seeker
                </option>
                <option value="employer" <?php echo ($user['role'] == 'employer') ? 'selected' : ''; ?>>Employer
                </option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active
                </option>
                <option value="suspended" <?php echo ($user['status'] == 'suspended') ? 'selected' : ''; ?>>
                    Suspended</option>
            </select>
        </div>
        <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>

</html>