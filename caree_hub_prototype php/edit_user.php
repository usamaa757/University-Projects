<?php
include 'header.php';
include "db_connect.php";
$msg = $error = "";
// Ensure only admin can access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
if (!$user) {
    header("Location: manage_users.php");
    exit();
}

// Handle form submission
if (isset($_POST['update_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Update user details
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $name, $email, $role, $status, $user_id);
    if ($stmt->execute()) {
        $msg = "User updated successfully!";
    } else {
        $error = "Failed to update user.";
    }
    $stmt->close();


    header("Location: manage_users.php?msg=$msg&error=$error");
    exit();
}
?>

<div class="container mt-5">
    <h2 class="text-center">Edit User</h2>

    <form method="post" class="bg-white p-4 rounded shadow-sm">
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
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="jobseeker" <?php echo ($user['role'] == 'jobseeker') ? 'selected' : ''; ?>>Job Seeker
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>