<?php
ob_start();
include 'header.php';
include '../db_connection.php';

// Fetch current student details
$student_id = $_SESSION['student_id'];
$sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $student = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching profile details!";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match only if a new password is provided
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $_SESSION['error'] = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        // Prepare the SQL query with optional password update
        $update_sql = "UPDATE students SET name = '$name', email = '$email'";

        // If a new password is provided, hash it and add it to the update query
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql .= ", password = '$hashed_password'";
        }

        $update_sql .= " WHERE student_id = '$student_id'";

        // Execute update
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['success'] = "<div class='alert alert-success'>Profile updated successfully!</div>";
            header("Location: student_profile.php");
            exit();
        } else {
            $_SESSION['error'] = "<div class='alert alert-danger'>Error updating profile!</div>";
        }
    }
}
?>
<br><br><br><br><br>
<div class="container" style="max-width: 600px;">
    <div class="card">
        <div class="card-header bg-primary ">
            <h3 class="text-center text-white">Edit Profile</h3>
        </div>
        <div class="card-body">
            <!-- Display Success or Error Message -->
            <?php
            if (isset($_SESSION['success'])) {
                echo $_SESSION['success'];
                unset($_SESSION['success']); // Clear message after displaying
            }
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']); // Clear message after displaying
            }
            ?>

            <form method="post" action="">
                <div class="form-group mt-3">
                    <label for="name"><strong>Name:</strong></label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($student['name']); ?>" required>
                </div>
                <div class="form-group mt-3">
                    <label for="email"><strong>Email:</strong></label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>
                <div class="form-group mt-3">
                    <label for="new_password"><strong>New Password:</strong></label>
                    <input type="password" class="form-control" id="new_password" name="new_password"
                        placeholder="Enter new password (optional)">
                </div>
                <div class="form-group mt-3">
                    <label for="confirm_password"><strong>Confirm Password:</strong></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                        placeholder="Confirm new password (optional)">
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include '../footer.php';
ob_end_flush();
?>