<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCourses = $_POST['courses'] ?? [];
    $adminMessage = trim($_POST['admin_message']);

    if (empty($selectedCourses) || empty($adminMessage)) {
        die('Please select at least one course and enter a message.');
    }

    // Prepare placeholders for SQL IN clause
    $placeholders = implode(',', array_fill(0, count($selectedCourses), '?'));

    $sql = "
        SELECT s.student_name, s.email, sa.row, sa.columns, c.course_name, sa.seat_number
        FROM seating_arrangements sa
        JOIN students s ON sa.student_id = s.student_id
        LEFT JOIN courses c ON s.course_id = c.course_id
        WHERE s.course_id IN ($placeholders)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($selectedCourses)), ...$selectedCourses);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
    if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
        echo "⚠️ Skipping invalid email: {$row['email']}<br>";
        continue;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pshwr030@gmail.com';
        $mail->Password   = 'asra zqic xfmi osko';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('pshwr030@gmail.com', 'Admin');
        $mail->addAddress($row['email'], $row['student_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Your Seating Arrangement';
        $mail->Body    = "
            Dear <strong>{$row['student_name']}</strong>,<br><br>
            Your seating arrangement for <strong>{$row['course_name']}</strong> is:<br>
            <strong>Seat No:</strong> {$row['seat_number']}<br>
        
            <em>Message from Admin:</em><br>
            {$adminMessage}<br><br>
            Regards,<br>Examination Team
        ";

        $mail->send();
        echo "✔️ Email sent to {$row['student_name']} ({$row['email']})<br>";
    } catch (Exception $e) {
        echo "❌ Failed to send to {$row['email']}. Error: {$mail->ErrorInfo}<br>";
    }
}
}

?>