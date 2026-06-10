<?php
include '../db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];

$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $password = $_POST['password'];

    // Update password if not empty
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $pass_stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $pass_stmt->bind_param("si", $hashed, $user_id);
        $pass_stmt->execute();
    }

    // Update users table
    $user_update = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=? WHERE user_id=?");
    $user_update->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
    $user_update->execute();

    echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
}


?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-3">
                <h4 class="text-center">Update Profile</h4>
                <div class="card-body">


                    <form method="POST">
                        <div class="mb-3">

                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name"
                                value="<?= htmlspecialchars($user_data['name']) ?>" required>
                        </div>

                        <div class="mb-3">

                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?= htmlspecialchars($user_data['email']) ?>" required>
                        </div>


                        <div class="mb-3">

                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                value="<?= htmlspecialchars($user_data['phone']) ?>" maxlength="11">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address"
                                value="<?= htmlspecialchars($user_data['address']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep old)</label>
                            <input type="password" class="form-control" name="password">
                        </div>


                        <div class="text-center">
                            <button type="submit" name="update_profile" class="btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>