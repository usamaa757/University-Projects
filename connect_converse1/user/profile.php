<?php
include 'header.php';
include "../db.php";

$user_id = $_SESSION['user_id'];

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
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetPath);
            $profile = $targetPath;
            $profile_pic = 'user/' . $profile;
        }
    }

    // Update logic
    if (!empty($password)) {
        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($profile_pic) {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, profile_pic=? WHERE user_id=?");
                $stmt->bind_param("ssssi", $name, $email, $hashed_password, $profile_pic, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE user_id=?");
                $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
            }
        } else {
            echo "<script>alert('Passwords do not match.');</script>";
            exit;
        }
    } else {
        // No password change
        if ($profile_pic) {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, profile_pic=? WHERE user_id=?");
            $stmt->bind_param("sssi", $name, $email, $profile_pic, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE user_id=?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }
    }

    if (isset($stmt) && $stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }
    if (isset($stmt)) {
        $stmt->close();
    }
}

// Fetch existing data
$query = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$query->bind_result($name, $email, $profile_pic);
$query->fetch();
$query->close();

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Profile</h4>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>"
                                required>
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
                            <?php if ($profile_pic): ?>
                            <img src="<?php echo str_replace("user/", "", $profile_pic); ?>" class="rounded-circle"
                                width="100" height="100" alt="Current Picture">
                            <?php endif; ?>

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