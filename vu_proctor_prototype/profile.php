<?php
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name   = $_POST['full_name'];
    $email       = $_POST['email'];
    $qual        = $_POST['qualifications'];
    $contact     = $_POST['contact_info'];
    $center      = $_POST['center_preferences'];
    $avail       = $_POST['availability'];
    $new_pass    = $_POST['password'];

    if (!empty($new_pass)) {
        // hash new password if provided
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET full_name=?, email=?, qualifications=?, contact_info=?, center_preferences=?, availability=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $full_name, $email, $qual, $contact, $center, $avail, $hashed, $user_id);
    } else {
        // keep old password
        $sql = "UPDATE users SET full_name=?, email=?, qualifications=?, contact_info=?, center_preferences=?, availability=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $full_name, $email, $qual, $contact, $center, $avail, $user_id);
    }

    if ($stmt->execute()) {
        $msg = "Profile updated successfully!";
    } else {
        $error = "Update failed!";
    }
}

$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="container">
    <h2>My Profile</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= $user['full_name'] ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>

        <label>Employee ID (Not Editable)</label>
        <input type="text" value="<?= $user['employee_id'] ?>" disabled>

        <label>CNIC (Not Editable)</label>
        <input type="text" value="<?= $user['cnic'] ?>" disabled>

        <label>Qualifications</label>
        <input type="text" name="qualifications" value="<?= $user['qualifications'] ?>">

        <label>Contact Info</label>
        <input type="text" name="contact_info" value="<?= $user['contact_info'] ?>">

        <label>Center Preferences</label>
        <input type="text" name="center_preferences" value="<?= $user['center_preferences'] ?>">

        <label>Availability</label>
        <textarea name="availability"><?= $user['availability'] ?></textarea>

        <label>Change Password (Optional)</label>
        <input type="password" name="password" placeholder="Leave blank to keep old password">

        <button type="submit">Update Profile</button>
    </form>
</div>
</body>

</html>