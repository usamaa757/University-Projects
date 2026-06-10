<?php
include 'header.php';
include "../db.php";

$user_id = $_SESSION['user_id'];

$msg = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_pic = "";

    // Handle file upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $targetDir = "uploads/";
        $ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $newName = uniqid() . "." . $ext;
            $targetPath = $targetDir . $newName;
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetPath)) {
                $profile_pic = 'user/' . $targetPath;
            }
        }
    }

    // Update logic
    if (!empty($password)) {
        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if ($profile_pic) {
                $sql = "UPDATE users SET name='$name', email='$email', password='$hashed_password', profile_pic='$profile_pic' WHERE user_id=$user_id";
            } else {
                $sql = "UPDATE users SET name='$name', email='$email', password='$hashed_password' WHERE user_id=$user_id";
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        // No password change
        if ($profile_pic) {
            $sql = "UPDATE users SET name='$name', email='$email', profile_pic='$profile_pic' WHERE user_id=$user_id";
        } else {
            $sql = "UPDATE users SET name='$name', email='$email' WHERE user_id=$user_id";
        }
    }

    if (empty($error) && isset($sql)) {
        if (mysqli_query($conn, $sql)) {
            $msg = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// Fetch existing data
$query = "SELECT name, email, profile_pic FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$name = $row['name'];
$email = $row['email'];
$profile_pic = $row['profile_pic'];
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">


            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($msg)): ?>
                        <p class="text-success"><?= htmlspecialchars($msg) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <p class="text-danger "><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>New Password (leave blank if no change)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Change Profile Picture</label>
                            <input type="file" name="profile_pic" class="form-control" accept="image/*">
                            <br>
                            <div class="text-center">

                            <?php if (!empty($profile_pic)): ?>
                                <img src="<?= str_replace("user/", "", $profile_pic) ?>" class="rounded-circle" width="100" height="100" alt="Current Picture">
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-dark">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>