<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$query = "SELECT full_name, study_program, about_me, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>User Profile</h2>

        <div class="profile-section">
            <?php if (!empty($user["profile_pic"])): ?>
            <img src="uploads/<?php echo htmlspecialchars($user["profile_pic"]); ?>" alt="Profile Picture"
                class="profile-pic">
            <?php else: ?>
            <img src="uploads/default.png" alt="Default Profile Picture" class="profile-pic">
            <?php endif; ?>

            <p><strong>Name:</strong> <?php echo htmlspecialchars($user["full_name"]); ?></p>
            <p><strong>Program:</strong> <?php echo htmlspecialchars($user["study_program"]); ?></p>
            <p><strong>About Me:</strong> <?php echo nl2br(htmlspecialchars($user["about_me"])); ?></p>
        </div>
    </div>

</body>

</html>