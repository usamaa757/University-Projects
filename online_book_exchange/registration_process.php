<?php
include("db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address']);

    // Password matching validation
    if ($password != $confirm_password) {
        header("Location: registration.php?msg=" . urlencode("Passwords do not match."));
        exit();
    }

    // Hashing the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);


    // Check if email already exists
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        header("Location: registration.php?msg=" . urlencode("Email already registered."));
        exit();
    }

    // Insert user into database
    $query = "INSERT INTO users (user_name, email, password, contact_number, address) 
              VALUES ('$full_name', '$email', '$hashed_password', '$contact_number', '$address')";
    if ($conn->query($query) === TRUE) {
        header("Location: registration.php?msg=" . urlencode("Registration successful..!"));
        exit();
    } else {
        header("Location: registration.php?msg=" . urlencode("Error: " . $conn->error));
        exit();
    }
}