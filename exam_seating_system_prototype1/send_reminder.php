<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Include PHPMailer

include 'db.php';  // Database connection
session_start();

// Fetch upcoming exams
$exam_query = mysqli_query($conn, "
    SELECT es.exam_id, c.course_name, es.exam_date, s.student_id, s.email
    FROM exam_schedule es
    JOIN courses c ON es.course_id = c.course_id
    JOIN student_exams se ON es.exam_id = se.exam_id
    JOIN students s ON se.student_id = s.student_id
    WHERE es.exam_date >= CURDATE() 
");

$students = [];
while ($row = mysqli_fetch_assoc($exam_query)) {
    $students[] = $row;
}

// Send emails to students
$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'louci786@gmail.com'; // Your Gmail
    $mail->Password = 'pecl zrvp onda hriy';  // Use App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('louci786@gmail.com', 'Exam Department');

    foreach ($students as $student) {
        $mail->addAddress($student['email']);  // Student Email
        $mail->Subject = "Exam Reminder: " . $student['course_name'];

        // Email Body
        $mail->Body = "Dear Student,\n\n"
            . "This is a reminder for your upcoming exam:\n"
            . "📌 Course: " . $student['course_name'] . "\n"
            . "📅 Date: " . $student['exam_date'] . "\n\n"
            . "Best of luck!\n"
            . "Exam Management Team";

        $mail->send();  // Send email
        $mail->clearAddresses();  // Clear for next student
    }

    echo "<script>alert('Exam reminders sent successfully!'); window.location.href='admin_dashboard.php';</script>";
} catch (Exception $e) {
    echo "<script>alert('Failed to send emails: {$mail->ErrorInfo}');</script>";
}