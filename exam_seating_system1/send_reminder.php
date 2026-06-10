<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db.php';
session_start();

if (!isset($_POST['students'])) {
    echo "<script>alert('No students selected!'); window.history.back();</script>";
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'louci786@gmail.com';
    $mail->Password = 'pxoc fwde cwft kfup'; // Be cautious with exposing this!
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('louci786@gmail.com', 'Course Department');
    $mail->isHTML(false);
    $mail->SMTPKeepAlive = true;

    foreach ($_POST['students'] as $student_json) {
        $student = json_decode($student_json, true);
        $mail->clearAddresses();
        $mail->addAddress($student['email']);
        $mail->Subject = "Reminder: " . $student['course_name'];
        $mail->Body = "Dear Student,\n\n"
            . "This is a reminder regarding your registered course:\n"
            . "📚 Course: " . $student['course_name'] . "\n\n"
            . "Best wishes in your studies!\n"
            . "Academic Affairs Team";

        $mail->send();
    }

    $mail->smtpClose();
    echo "<script>alert('Selected reminders sent successfully!'); window.location.href='admin_dashboard.php';</script>";
} catch (Exception $e) {
    echo "<script>alert('Failed to send emails: {$mail->ErrorInfo}');</script>";
}