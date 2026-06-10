<?php
include 'header.php';
include '../other/db_connection.php';

$message = ""; // Initialize message variable
$messageClass = ""; // Initialize message CSS class

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $teacher_name = $_POST['teacher_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Upload profile picture
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $profile_pic_name = $_FILES['profile_pic']['name'];
        $profile_pic_tmp_name = $_FILES['profile_pic']['tmp_name'];
        $profile_pic_destination = '../uploads' . $profile_pic_name;
        if (move_uploaded_file($profile_pic_tmp_name, $profile_pic_destination)) {
            $profile_pic = $profile_pic_destination;
        } else {
            $message = "Failed to upload profile picture.";
            $messageClass = "error";
        }
    }

    // Insert teacher into the database
    $query = "INSERT INTO teachers (teacher_name, email, password, profile_pic) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $teacher_name, $email, $hashed_password, $profile_pic);
    if ($stmt->execute()) {
        $message = "Teacher added successfully.";
        $messageClass = "success";
    } else {
        $message = "Failed to add teacher.";
        $messageClass = "error";
    }

    // Close the statement after insertion
    $stmt->close();

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Teacher</title>
    <link rel="stylesheet" href="../css/form.css">
    <style>
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <header>
        <h2>Add Teacher</h2>
    </header>
    <form method="POST" action="" enctype="multipart/form-data">
        Teacher Name: <input type="text" name="teacher_name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Profile Picture: <input type="file" name="profile_pic" accept="image/*"><br>
        <button type="submit">Add Teacher</button>
    </form>
    <!-- Display the message below the form with color -->
    <p class="<?php echo $messageClass; ?>"><?php echo $message; ?></p>
</body>
</html>
