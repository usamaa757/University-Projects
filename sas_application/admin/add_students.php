<?php
// Include database connection
include '../other/db_connection.php';
// Message variable to display feedback
$message = '';

// Function to fetch classes from the classes table
function fetchClasses($conn) {
    $sql = "SELECT class_id, class_name FROM classes";
    $result = $conn->query($sql);
    $classes = [];
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $classes[] = $row;
            }
        } else {
            echo "No classes found in the database.";
        }
    } else {
        echo "Error fetching classes: " . $conn->error;
    }
    return $classes;
}
$classes = fetchClasses($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $student_name = $_POST['student_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $class_id = $_POST['class_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Handle file upload
    $profile_pic = $_FILES['profile_pic']['name'];
    $profile_pic_tmp = $_FILES['profile_pic']['tmp_name'];
    $profile_pic_path = 'uploads' . basename($profile_pic);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($profile_pic_tmp, $profile_pic_path)) {
        // Prepare SQL statement to insert student data
        $stmt = $conn->prepare("INSERT INTO students (student_name, dob, gender, class_id, email, password, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $student_name, $dob, $gender, $class_id, $email, $hashed_password, $profile_pic_path);
        
        // Execute the statement
        if ($stmt->execute()) {
            $message = 'Student added successfully!';
        } else {
            $message = 'Error adding student: ' . $stmt->error;
        }
        
        // Close the statement
        $stmt->close();
    } else {
        $message = 'Error uploading profile picture!';
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="../css/manage.css">
</head>
<body>

<!-- Display feedback message -->
<?php if ($message): ?>
    <p><?= htmlspecialchars($message); ?></p>
<?php endif; ?>

<!-- Student form -->
<form method="POST" enctype="multipart/form-data">
    <header>
        <h2>Add Student</h2>
    </header>
    <label for="student_name">Student Name:</label>
    <input type="text" id="student_name" name="student_name" required><br>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" required><br>

    <label for="gender">Gender:</label>
    <input type="text" id="gender" name="gender" required><br>

    <label for="class_id">Class:</label>
    <select id="class_id" name="class_id" required>
        <!-- Populate dropdown with classes -->
        <?php foreach ($classes as $class): ?>
            <option value="<?= htmlspecialchars($class['class_id']); ?>"><?= htmlspecialchars($class['class_name']); ?></option>
        <?php endforeach; ?>
    </select><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="profile_pic">Profile Picture:</label>
    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" required><br>

    <input type="submit" value="Add Student">
</form>

</body>
</html>
