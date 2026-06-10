<?php
// Start the session
session_start();
$student_id = $_SESSION['student_id'];

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: user-login.php"); // Redirect to login page if not logged in
    exit();
}

// Include your database connection file
include('db_connect.php');

// Fetch the user's current details from the database
$student_id = $_SESSION['student_id'];
$sql = "SELECT * FROM users_reg WHERE id = $student_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle form submission for updating user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Update the user's details in the database
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users_reg SET full_name='$full_name', email='$email', password='$hashed_password' WHERE id=$student_id";
    } else {
        $update_sql = "UPDATE users_reg SET full_name='$full_name', email='$email' WHERE id=$student_id";
    }

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Profile updated successfully!');</script>";
        // Update session variables
        $_SESSION['full_name'] = $full_name;
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
            <a href="stud_dashboard.php">Back</a>
        </button>
        <h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>
        <div class="profile-box">
            <h3>Your Profile</h3>
            <form action="user-profile.php" method="POST">
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" name="full_name" value="<?php echo $user['full_name']; ?>" required>

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
