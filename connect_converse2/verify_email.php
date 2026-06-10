<?php
include 'db.php';

$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->affected_rows > 0) {
   $msg ='Email verified! You can now log in';
   header("Location: login.php?msg=" . urlencode($msg));
   
} else {
    $error ='Invalid or expired token';
   header("Location: register.php?error=" . urlencode($error));
}