<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../db_connection.php');

// Get the current user ID from the session
$user_id = $_SESSION['user_id'];

// Get form input
$username = $_POST['username'];
$email = $_POST['email'];
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

$conn->begin_transaction();

try {
    // Update query
    $sql = "UPDATE users SET username = ?, email = ?" . ($password ? ", password = ?" : "") . " WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($password) {
        $stmt->bind_param("sssi", $username, $email, $password, $user_id);
    } else {
        $stmt->bind_param("ssi", $username, $email, $user_id);
    }

    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $_SESSION['success'] = 'Profile updated successfully!';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Failed to update profile: ' . $e->getMessage();
}

$conn->close();

header("Location: user_profile.php");
exit();
