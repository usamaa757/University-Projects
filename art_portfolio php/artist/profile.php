<?php
include '../db.php';
include 'header.php';

$artist_id = $_SESSION['user_id'];
$message = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $style = mysqli_real_escape_string($conn, $_POST['style']);
    $project = mysqli_real_escape_string($conn, $_POST['project_description']);

    $update = "UPDATE users 
               SET bio = '$bio', style = '$style', project_description = '$project' 
               WHERE user_id = '$artist_id' AND role = 'artist'";
    if (mysqli_query($conn, $update)) {
        $message = "Profile updated successfully!";
    } else {
        $error = "Error updating profile.";
    }
}

// Fetch current data
$result = mysqli_query($conn, "SELECT bio, style, project_description FROM users WHERE user_id = '$artist_id'");
$data = mysqli_fetch_assoc($result);
?>

<h2>Edit Artist Profile</h2>
<div class="form-container">

    <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="forms">
        <label>Bio</label>
        <textarea name="bio" rows="4" required><?= htmlspecialchars($data['bio']) ?></textarea>

        <label>Artistic Style</label>
        <input type="text" name="style" value="<?= htmlspecialchars($data['style']) ?>" required>

        <label>Project Description</label>
        <textarea name="project_description" rows="4"
            required><?= htmlspecialchars($data['project_description']) ?></textarea>

        <div class="text-center">
            <button type="submit" class="btn">Update</button>
        </div>
    </form>
</div>