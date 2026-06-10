<?php
include '../other/navbar.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/ls.css">
    <style>
        body {
            background-color: #b5dd7c;
            font-family: Arial, sans-serif;
        }

        .login-container {
            margin-top: 80px;
            width: 400px;
            margin: auto;
            padding-top: 20px;
            padding-bottom: 40px;
        }

        .login-header {
            text-align: center;
        }

        .login-input {
            width: 93%;
            padding: 10px;
            margin-bottom: 15px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #0f582d;
            color: white;
            border: none;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <br>
    <br><br><br><br>
    <div class="login-container">
        <form id="login-form" action="" method="post">
            <h2 class="login-header">Login</h2>

            <label for="role">Select Role:</label>
            <select name="role" id="role" class="login-input" required>
                <option value="admin">Admin</option>
                <option value="teachers">Teacher</option>
                <option value="students">Student</option>
                <option value="parents">Parent</option>
            </select>

            <input type="email" class="login-input" id="email" name="email" placeholder="Enter your email" required>
            <input type="password" class="login-input" id="password" name="password" placeholder="Enter your password" required>
            <input type="submit" class="login-btn" value="Login">
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="../other/register.php">Register</a></p>
        </div>
    </div>

    <?php
    // Include the database connection file
    include '../other/db_connection.php';

    // Start a session
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get user input
        $role = $_POST['role'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Initialize variables for queries
        $sql = "";
        $user_id = "";
        $redirect_page = "";

        // Determine the role and set the appropriate SQL query
        if ($role === 'admin') {
            $sql = "SELECT * FROM admin WHERE email = ? AND status = 'approved'";
            $user_id = 'admin_id';
            $redirect_page = '../admin/admin_dashboard.php';
        } elseif ($role === 'teachers') {
            $sql = "SELECT * FROM teachers WHERE email = ? AND status = 'approved'";
            $user_id = 'teacher_id';
            $redirect_page = '../teacher/teacher_dashboard.php';
        } elseif ($role === 'students') {
            $sql = "SELECT * FROM students WHERE email = ? AND status = 'approved'";
            $user_id = 'student_id';
            $redirect_page = '../student/display_course.php';
        } elseif ($role === 'parents') {
            $sql = "SELECT * FROM parents WHERE email = ? AND status = 'approved'";
            $user_id = 'parent_id';
            $redirect_page = '../parent/parent_dashboard.php';
        } else {
            echo "<p class='error-message'>Invalid role selected.</p>";
            exit();
        }

        // Prepare SQL statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user_data['password'])) {
                // Set session variables and redirect
                $_SESSION['user_id'] = $user_data[$user_id];
                $_SESSION['teacher_name'] = $user_data['teacher_name'];
                $_SESSION['student_name'] = $user_data['student_name'];
                $_SESSION['admin_name'] = $user_data['admin_name'];
                $_SESSION['parent_name'] = $user_data['parent_name'];
                $_SESSION['class_id'] = $user_data['class_id'];
                $_SESSION['role'] = $role;
                header("Location: " . $redirect_page);
                exit();
            } else {
                echo "<p class='error-message'>Invalid password.</p>";
            }
        } else {
            echo "<p class='error-message'>Invalid email or account not approved.</p>";
        }

        // Close the statement
        $stmt->close();
    }

    // Close the connection
    $conn->close();
    ?>
</body>
</html>
