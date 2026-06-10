<?php
session_start();
include '../db_connection.php';

// Initialize error message
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email and password from the login form
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM registration WHERE student_email = ? AND status = 'approved'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, verify password
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Password is correct, start session and set session variables
            session_regenerate_id(true); // Prevent session fixation attacks
            $_SESSION['student_name'] = htmlspecialchars($row['student_name']);
            $_SESSION['student_email'] = htmlspecialchars($row['student_email']);
            $_SESSION['student_id'] = htmlspecialchars($row['student_id']);
            $_SESSION['profile_pic'] = htmlspecialchars($row['picture']); // Store profile picture in session

            // Redirect to the student dashboard
            header("Location: ../student/student_dashboard.php");
            exit();
        } else {
            // Password is incorrect, display error message
            $errorMsg = "Invalid password.";
        }
    } else {
        // User does not exist or is not approved, display error message
        $errorMsg = "Student is not registered or not yet approved.";
    }

    $stmt->close(); // Close the statement
}

$conn->close(); // Close the database connection
?>

<!-- Display the error message -->
<?php if (!empty($errorMsg)): ?>
    <div class="error-message">
        <p><?php echo $errorMsg; ?></p>
    </div>
<?php endif; ?>
