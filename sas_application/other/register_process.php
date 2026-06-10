<?php
// Include the database connection file
require_once "db_connection.php";


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define variables and initialize with empty values
    $role = "";
    $role_err = "";

    // Validate role
    if (empty($_POST["role"])) {
        $role_err = "Please select a role.";
    } else {
        $role = $_POST["role"];
    }

    // Assuming other validations (for email, password, etc.) are done on the respective role-based registration pages

    // Redirect based on the selected role
    if (empty($role_err)) {
        switch ($role) {
            case 'student':
                header("Location: ../student/student_reg.php");
                exit();
            case 'teacher':
                header("Location: ../teacher/teacher_reg.php");
                exit();
            case 'parent':
                header("Location: ../parent/parent_reg.php");
                exit();
            default:
                // Redirect back to the signup page with an error message if the role is invalid
                header("Location: signup.php?error=InvalidRole");
                exit();
        }
    }
    // If there's a role error, handle it appropriately (not shown here)
}

// Close connection (if not using persistent connections)
$conn->close();
?>
