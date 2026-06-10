<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include '../include_files/db_connection.php';

    // Sanitize input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $course_id = htmlspecialchars(trim($_POST['course_id']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Initialize error message variable
    $errorMsg = "";

    // Ensure passwords match
    if ($password !== $confirm_password) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Check if email already exists
        $email_check_query = "SELECT * FROM registration WHERE student_email=?";
        if ($stmt = $conn->prepare($email_check_query)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errorMsg = "Email already exists.";
            }
            $stmt->close();
        }

        // Check if phone number already exists
        if (empty($errorMsg)) {
            $phone_check_query = "SELECT * FROM registration WHERE phone=?";
            if ($stmt = $conn->prepare($phone_check_query)) {
                $stmt->bind_param("s", $phone);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errorMsg = "Phone number already exists.";
                }
                $stmt->close();
            }
        }

        // Proceed with registration if no errors
       
          
            if (empty($errorMsg)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the SQL statement
                $sql = "INSERT INTO registration (student_name, student_email, course_id, gender, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?)";

                // Initialize the statement
                if ($stmt = $conn->prepare($sql)) {
                    // Bind the variables to the statement
                    $stmt->bind_param("ssisss", $name, $email, $course_id, $gender, $phone, $hashed_password);

                    // Execute the statement
                    if ($stmt->execute()) {
                        // Get the ID of the newly inserted student
                        $student_id = $stmt->insert_id;

                        // Insert into student_course table
                        $student_course_sql = "INSERT INTO student_course (student_id, course_id) VALUES (?, ?)";
                        if ($stmt = $conn->prepare($student_course_sql)) {
                            $stmt->bind_param("ii", $student_id, $course_id);
                            if ($stmt->execute()) {
                                $resultMsg = "Registration successful and course assigned!";
                            } else {
                                $errorMsg = "Error: Could not assign course. " . $conn->error;
                            }
                            
                        } else {
                            $errorMsg = "Error: Could not prepare the query: $student_course_sql. " . $conn->error;
                        }
                    } else {
                        $errorMsg = "Error: " . $conn->error;
                    }
                    // Close the statement
                    $stmt->close();
                } else {
                    $errorMsg = "Error: Could not prepare the query: $sql. " . $conn->error;
                }
            
        }
    }

    // Close the database connection
    $conn->close();
}
?>
