<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

// Function to send email via Gmail SMTP
function sendEmail($to, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'louci786@gmail.com'; // Your Gmail
        $mail->Password   = 'pecl zrvp onda hriy'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Headers
        $mail->setFrom('louci786@gmail.com', 'Exam Office');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Assign Seats and Send Emails
if (isset($_POST['assign_seats'])) {
    include 'db.php';

    $room_assignments = $_POST['room_assignments'];
    $students = [];

    // Fetch students & their emails
    $student_query = mysqli_query($conn, "
        SELECT se.student_id, se.exam_id, e.course_id, s.email 
        FROM student_exams se
        JOIN exam_schedule e ON se.exam_id = e.exam_id
        JOIN students s ON se.student_id = s.student_id
        ORDER BY RAND()"); // Shuffle students for randomization

    while ($row = mysqli_fetch_assoc($student_query)) {
        $students[] = $row;
    }

    $errors = [];
    $seat_plan = []; // Track seat assignments

    foreach ($room_assignments as $student_id => $room_id) {
        // Check if student is already assigned
        $check_query = mysqli_query($conn, "SELECT * FROM seat_assignments WHERE student_id = '$student_id' AND room_id = '$room_id'");
        if (mysqli_num_rows($check_query) > 0) {
            $errors[] = "Student ID $student_id is already assigned a seat in this room.";
            continue;
        }

        // Fetch room details
        $room_query = mysqli_query($conn, "SELECT * FROM rooms WHERE room_id = '$room_id'");
        $room = mysqli_fetch_assoc($room_query);
        $available_seats = $room['available_seats'];
        $total_seats = $room['total_seats'];

        if ($available_seats > 0) {
            foreach ($students as $student) {
                if ($student['student_id'] == $student_id) {
                    $exam_id = $student['exam_id'];
                    $course_id = $student['course_id'];
                    $student_email = $student['email'];

                    // Find seat ensuring course gap
                    $seat_number = null;
                    for ($i = 1; $i <= $total_seats; $i++) {
                        if (!isset($seat_plan[$i])) {
                            // Ensure the course gap
                            if (
                                ($i > 1 && isset($seat_plan[$i - 1]) && $seat_plan[$i - 1]['course_id'] == $course_id) ||
                                ($i < $total_seats && isset($seat_plan[$i + 1]) && $seat_plan[$i + 1]['course_id'] == $course_id)
                            ) {
                                continue; // Skip seat if previous or next seat has the same course
                            }
                            $seat_number = $i;
                            $seat_plan[$i] = [
                                'student_id' => $student_id,
                                'exam_id' => $exam_id,
                                'course_id' => $course_id
                            ];
                            break;
                        }
                    }

                    if ($seat_number) {
                        // Assign seat in DB
                        mysqli_query($conn, "INSERT INTO seat_assignments (student_id, exam_id, room_id, seat_number) 
                                             VALUES ('$student_id', '$exam_id', '$room_id', '$seat_number')");

                        // Reduce available seats
                        mysqli_query($conn, "UPDATE rooms SET available_seats = available_seats - 1 WHERE room_id = '$room_id'");

                        // Send Email Notification
                        $subject = "Exam Seating Assignment";
                        $message = "
                            <html>
                            <head>
                                <title>Exam Seating Assignment</title>
                            </head>
                            <body>
                                <p>Dear Student,</p>
                                <p>Your exam seat has been assigned successfully.</p>
                                <p><strong>Exam ID:</strong> $exam_id</p>
                                <p><strong>Room:</strong> {$room['room_name']}</p>
                                <p><strong>Seat Number:</strong> $seat_number</p>
                                <p>Please arrive 30 minutes before the exam starts.</p>
                                <p>Best regards,</p>
                                <p>Exam Seating Arrangement System</p>
                            </body>
                            </html>
                        ";

                        if (sendEmail($student_email, $subject, $message)) {
                            echo "Email sent to $student_email.<br>";
                        } else {
                            echo " Failed to send email to $student_email.<br>";
                        }
                    }
                }
            }
        }
    }

    if (!empty($errors)) {
        echo "<script>alert('Some students already have assigned seats:\\n" . implode("\\n", $errors) . "'); window.location.href='view_seating.php';</script>";
    } else {
        echo "<script>alert('Seats assigned successfully! Emails sent.'); window.location.href='view_seating.php';</script>";
    }
}