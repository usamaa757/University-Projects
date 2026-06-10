<?php
// Start the session
session_start();
$admin_id = $_SESSION['admin_id'];

// Check if the admin is logged in, otherwise redirect to the login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php';

// Fetch the user's current details from the database
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admins WHERE id = $admin_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle form submission for updating user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Update the user's details in the database
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE admins SET name='$name', email='$email', password='$hashed_password' WHERE id=$admin_id";
    } else {
        $update_sql = "UPDATE admins SET name='$name', email='$email' WHERE id=$admin_id";
    }

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Profile updated successfully!');</script>";
        // Update session variables
        $_SESSION['name'] = $name;
        // Refresh the page to reflect changes
        header("Refresh:0");
    } else {
        echo "<script>alert('Error updating profile: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="CSS/user-profile-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <div class="container profile-container">
        <button class="form back-button">
            <a href="admin-dashboard.php">Back</a>
        </button>

        <h1>Your Profile</h1>
        <div class="profile-box">
            <form method="POST">
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" name="name" value="<?php echo $user['name']; ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>

                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>