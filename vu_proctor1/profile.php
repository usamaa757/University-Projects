<?php
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name   = $conn->real_escape_string($_POST['full_name']);
    $email       = $conn->real_escape_string($_POST['email']);
    $qual        = $conn->real_escape_string($_POST['qualifications']);
    $contact     = $conn->real_escape_string($_POST['contact_info']);
    $center      = $conn->real_escape_string($_POST['center_preferences']);
    $avail       = $conn->real_escape_string($_POST['availability']);
    $new_pass    = $_POST['password'];
    $user_id     = intval($user_id); // ensure numeric

    if (!empty($new_pass)) {
        // hash new password if provided
        $hashed = $conn->real_escape_string(password_hash($new_pass, PASSWORD_DEFAULT));

        $sql = "
            UPDATE users 
            SET full_name='$full_name', 
                email='$email', 
                qualifications='$qual', 
                contact_info='$contact', 
                center_preferences='$center', 
                availability='$avail', 
                password='$hashed'
            WHERE id=$user_id
        ";
    } else {
        // keep old password
        $sql = "
            UPDATE users 
            SET full_name='$full_name', 
                email='$email', 
                qualifications='$qual', 
                contact_info='$contact', 
                center_preferences='$center', 
                availability='$avail'
            WHERE id=$user_id
        ";
    }

    if ($conn->query($sql) === TRUE) {
        $msg = "Profile updated successfully!";
    } else {
        $error = "Update failed! " . $conn->error;
    }
}

// Fetch user data
$user_id = intval($user_id);
$sql = "SELECT * FROM users WHERE id=$user_id LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

?>

<div class="container">
    <h2><?= $user['role'] ?> Profile </h2>
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
        <select name="availability" required>
            <option value="Morning" <?= ($user['availability'] == 'Morning') ? 'selected' : '' ?>>Morning</option>
            <option value="afternoon" <?= ($user['availability'] == 'afternoon') ? 'selected' : '' ?>>Afternoon</option>
            <option value="Leave" <?= ($user['availability'] == 'leave') ? 'selected' : '' ?> selected disabled>Leave
            </option>
        </select>

        <label>Change Password (Optional)</label>
        <input type="password" name="password" placeholder="Leave blank to keep old password">

        <button type="submit">Update Profile</button>
    </form>
</div>
</body>

</html>