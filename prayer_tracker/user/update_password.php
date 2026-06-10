<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['new_password'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
$stmt->bind_param("si", $new_password, $user_id);
$stmt->execute();
$stmt->close();

$_SESSION['msg'] = "Password updated!";
header("Location: profile.php");
exit();