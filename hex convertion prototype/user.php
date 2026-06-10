<?php
require 'database_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all users except the logged-in user
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">
        <h2>Registered Users</h2>
        <div class="users-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
            <div class="user-card">
                <img src="images/<?= htmlspecialchars($row['profile_pic']); ?>" alt="Profile Picture">
                <h3><?= htmlspecialchars($row['full_name']); ?></h3>
                <p><strong>Study Program:</strong> <?= htmlspecialchars($row['study_program']); ?></p>
                <p><?= htmlspecialchars($row['about_me']); ?></p>
            </div>
            <?php }
            } else {
                echo "<p>No registered users found.</p>";
            }
            ?>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>