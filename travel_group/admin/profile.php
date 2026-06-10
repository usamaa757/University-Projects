<?php
include 'header.php';
require '../db.php';


$admin_id = $_SESSION['admin_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            echo "<script>alert('New passwords do not match.');</script>";
        } else {
            // Get current hashed password from DB
            $query = "SELECT password_hash FROM admin WHERE admin_id = $admin_id";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                $stored_hash = $row['password_hash'];

                if (password_verify($current_password, $stored_hash)) {
                    // Hash new password
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update DB
                    $update_query = "UPDATE admin SET password_hash = '$new_hashed_password' WHERE admin_id = $admin_id";
                    if (mysqli_query($conn, $update_query)) {
                        echo "<script>alert('Password changed successfully.'); window.location.href='dashboard.php';</script>";
                    } else {
                        echo "<script>alert('Failed to update password.');</script>";
                    }
                } else {
                    echo "<script>alert('Current password is incorrect.');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('Please fill all fields.');</script>";
    }

    mysqli_close($conn);
}
?>

<div class="form-container">
    <h2>Change Password</h2>
    <form method="post" class="forms">
        <label>Current Password:</label>
        <input type="password" name="current_password" required>

        <label>New Password:</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" required>

        <div class="text-center">

            <button class="btn" type="submit">Update</button>
        </div>
    </form>
</div>

</body>

</html>