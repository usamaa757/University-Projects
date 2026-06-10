<?php
include 'header.php';
include '../db.php';

$customer_id = $_SESSION['customer_id'];

$query = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$owner = mysqli_fetch_assoc($result);

if (!$owner) {
    echo "Profile not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];

    $update_query = "";
    $update_stmt = null;

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if ($password !== $confirm) {
            echo "<script>alert('Passwords do not match');</script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE customer SET name = ?, email = ?, password = ?, phone_number = ?, address = ? WHERE customer_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sssssi", $name, $email, $hashed_password, $phone, $address, $customer_id);
        }
    } else {
        $update_query = "UPDATE customer SET name = ?, email = ?, phone_number = ?, address = ? WHERE customer_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssssi", $name, $email, $phone, $address, $customer_id);
    }

    if ($update_stmt && mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "Error updating profile.";
    }
}
?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Edit Profile</h2>
    </div>

    <form method="POST" class="form">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($owner['name']) ?>" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($owner['email']) ?>" required><br><br>

        <label>Phone Number:</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($owner['phone_number'] ?? '') ?>"
            required><br><br>

        <label>Address:</label>
        <textarea name="address" required><?= htmlspecialchars($owner['address'] ?? '') ?></textarea><br><br>

        <label>New Password (optional):</label>
        <input type="password" name="password" placeholder="Enter new password"><br><br>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" placeholder="Confirm new password"><br><br>

        <div class="btn-div">
            <button type="submit" class="btn">Update</button>
        </div>
    </form>
</div>
</body>

</html>