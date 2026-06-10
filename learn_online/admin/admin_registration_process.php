<?php
// Database connection details
include '../db_connection.php';

$resultMsg = '';
$errorMsg = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $pic = $_FILES['picture']['name']; // Get the file name
    $pic_tmp = $_FILES['picture']['tmp_name']; // Get the temporary file path

    // Check if password matches confirm password
    if ($password !== $confirmPassword) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Check if email already exists
        $email_check_query = "SELECT * FROM admin WHERE Email=?";
        $stmt = $conn->prepare($email_check_query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $email_check_result = $stmt->get_result();

        if ($email_check_result->num_rows > 0) {
            $errorMsg = "Email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Move uploaded file to a designated folder
            $upload_dir = '../assets/profile_pic/'; // Ensure this directory exists and is writable
            $upload_file = $upload_dir . basename($pic);

            if (move_uploaded_file($pic_tmp, $upload_file)) {
                // Insert data into the database
                $insert_data_query = "INSERT INTO admin(`name`,`email`, `password`, `pic`) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_data_query);
                $stmt->bind_param('ssss', $name, $email, $hashed_password, $upload_file);
                if ($stmt->execute()) {
                    $resultMsg = "Registration successful!";
                } else {
                    $errorMsg = "Error: " . $conn->error;
                }
          
            } else {
                $errorMsg = "Failed to upload the image.";
            }
        }
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>