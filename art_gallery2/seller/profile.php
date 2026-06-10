<?php
include 'header.php';
include '../db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $address, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully.'); window.location='profile.php';</script>";
    } else {
        echo "Update failed: " . $stmt->error;
    }
    $stmt->close();
}
?>

<div class="col-md-5 mx-auto border rounded shadow mt-5 ">
    <h3 class="text-center">Edit Profile</h3>
    <form method="post" action="profile.php" class="p-3">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['username']); ?>"
                required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>"
                required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">New Password <small class="text-muted">(leave blank if not
                    changing)</small></label>
            <input type="password" name="password" class="form-control" placeholder="New Password">
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
</div>