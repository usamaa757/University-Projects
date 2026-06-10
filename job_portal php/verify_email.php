<?php
include 'db.php';

$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Email verified! You can now log in');window.location.href='login.php';</script>";
} else {
    echo "<script>alert('Invalid or expired token.');window.location.href='register.php';</script>";
}