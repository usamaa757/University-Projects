<?php
// Start output buffering
ob_start();

include 'header.php';
// Include the database connection file
include '../other/db_connection.php';

// Assuming admin_id is stored in the session when admin logs in
$admin_id = $_SESSION['user_id'];

// Fetch existing admin data
$stmt = $conn->prepare("SELECT admin_name, email FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = $_POST['admin_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL for updating admin details
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update with password
        $stmt = $conn->prepare("UPDATE admin SET admin_name = ?, email = ?, password = ? WHERE admin_id = ?");
        $stmt->bind_param("sssi", $admin_name, $email, $hashed_password, $admin_id);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("UPDATE admin SET admin_name = ?, email = ? WHERE admin_id = ?");
        $stmt->bind_param("ssi", $admin_name, $email, $admin_id);
    }

    // Execute the update query
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Redirect to the same page with a message
    header("Location: profile.php?message=" . urlencode($message));
    exit;
}

// Close the connection
$conn->close();

// Flush the output buffer
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../css/form.css">
    <style>
      
   
      
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        /* .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        } */
      
        
        .message {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <?php
            // Display message if available
            if (isset($_GET['message'])) {
                echo "<p class='message'>" . htmlspecialchars($_GET['message']) . "</p>";
            }
            ?>
        <div style="text-align: center; margin-top: 10px;">
        
        <h3>Update Profile</h3>
    </div>
            <div class="form-group">
                <label for="admin_name">Admin Name:</label>
                <input type="text" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($admin_data['admin_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
            </div>
        </form>
    </div>
</body>
</html>
