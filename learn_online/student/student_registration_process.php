<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include '../db_connection.php';

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
    $resultMsg = "";

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
            // Handle file upload for picture
            $pic = "";
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                // Check file size (limit to 1 MB)
                $maxFileSize = 1 * 1024 * 1024; // 1 MB in bytes
                if ($_FILES['picture']['size'] > $maxFileSize) {
                    $errorMsg = "File size exceeds 1 MB.";
                } else {
                    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                    $file_extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);

                    if (in_array($file_extension, $allowed_extensions)) {
                        $pic = "../assets/profile_pic/" . basename($_FILES['picture']['name']);
                        if (!move_uploaded_file($_FILES['picture']['tmp_name'], $pic)) {
                            $errorMsg = "Failed to upload picture.";
                        }
                    } else {
                        $errorMsg = "Invalid file type for picture.";
                    }
                }
            } else {
                if (isset($_FILES['picture'])) {
                    $errorMsg = "Picture upload error: " . $_FILES['picture']['error'];
                } else {
                    $errorMsg = "Picture not provided.";
                }
            }

            if (empty($errorMsg)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the SQL statement
                $sql = "INSERT INTO registration (student_name, student_email, course_id, gender, phone, picture, password) VALUES (?, ?, ?, ?, ?, ?, ?)";

                // Initialize the statement
                if ($stmt = $conn->prepare($sql)) {
                    // Bind the variables to the statement
                    $stmt->bind_param("ssissss", $name, $email, $course_id, $gender, $phone, $pic, $hashed_password);

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
    }

    // Close the database connection
    $conn->close();
}
?>
