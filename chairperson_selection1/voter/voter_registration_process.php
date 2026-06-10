<?php
include '../db_connection.php';
$errorMsg = '';
$resultMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $student_id = $_POST['student_id'];
    $department = $_POST['department'];

    // Check if the student ID already exists
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM voter_registration WHERE student_id = ?");
    $check_stmt->bind_param("s", $student_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $errorMsg = 'Student ID already registered. Please use a different ID.';
    } else {
        // Generate unique Voter ID and Password
        $voter_id = 'voter' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Generates a Voter ID like voter1234
        $password = bin2hex(random_bytes(4)); // Generates a random 8-character password

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO voter_registration (name, gender, student_id, department, voter_id, password, plain_password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $gender, $student_id, $department, $voter_id, $hashed_password, $password);

        // Execute the statement
        if ($stmt->execute()) {
            $resultMsg = "Registration successful. Your Voter ID is $voter_id and your Password is $password. Please keep them safe.";
        } else {
            $errorMsg = 'Registration failed. Please try again.';
        }

        // Close the statement
        $stmt->close();
    }

    // Close the connection
    $conn->close();
}
?>
         