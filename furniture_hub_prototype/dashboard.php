<?php
include("config.php");
include("navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // redirect if not logged in
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email, role FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$name = $user['name'];
$email = $user['email'];
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($role); ?> Dashboard - Furniture Hub</title>
    <link rel="stylesheet" href="styles.css">
    <style>

    </style>
</head>

<body>

    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($name); ?> 👋</h2>
        <p>You are logged in as <strong><?php echo $email; ?></strong> (<?php echo ucfirst($role); ?>).</p>

        <div class="dashboard-links">
            <?php if ($role === "seller"): ?>
                <a href="furniture.php">Manage My Furniture</a>
                <a href="profile.php">Edit Profile</a>
            <?php elseif ($role === "buyer"): ?>
                <a href="furniture_list.php">Browse Furniture</a>
                <a href="profile.php">Edit Profile</a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>