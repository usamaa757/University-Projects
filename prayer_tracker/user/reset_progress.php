<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['reset_today'])) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("DELETE FROM prayer_records WHERE user_id = ? AND date = ?");
    $stmt->bind_param("is", $user_id, $today);
    if ($stmt->execute()) {
        echo "<script>
            alert('Today ($today) progress reset!');
            window.location.href = 'profile.php';
        </script>";
        exit;
    }
    $stmt->close();
}

if (isset($_POST['reset_qaza'])) {
    $stmt = $conn->prepare("DELETE FROM prayer_records WHERE user_id = ? AND status = 'qaza'");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<script>
            alert('qaza prayer reset!');
            window.location.href = 'profile.php';
        </script>";
        exit;
    }
    $stmt->close();
}

$conn->close();

// Optional fallback in case no POST action matched
header("Location: profile.php");
exit();
