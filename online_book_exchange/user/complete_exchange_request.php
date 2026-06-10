<?php

include '../db_connection.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
    $update_status = false;

    if (isset($_GET['accept'])) {
        $stmt = $conn->prepare("UPDATE exchange_requests SET status = 'accepted', exchange_status = 'pickup' WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);
        $update_status = $stmt->execute();
        $order_status = "Accepted";
    } elseif (isset($_GET['decline'])) {
        $stmt = $conn->prepare("UPDATE exchange_requests SET status = 'declined' WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);
        $update_status = $stmt->execute();
        $order_status = "Declined";
    } elseif (isset($_GET['deliver'])) {
        $stmt = $conn->prepare("UPDATE exchange_requests SET exchange_status = 'delivered' WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);
        $update_status = $stmt->execute();
        $order_status = "Delivered";
    }

    if ($update_status) {
        // Fetch user email
        $email_stmt = $conn->prepare("SELECT u.email FROM users u 
                                      JOIN exchange_requests er ON u.user_id = er.requested_by 
                                      WHERE er.request_id = ?");
        $email_stmt->bind_param("i", $request_id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        $user_email = $email_result->fetch_assoc()['email'];

        // Set up PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'louci786@gmail.com';  // Your email
            $mail->Password = 'ueak wnnr ixnr hhxw'; // Your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('louci786@gmail.com', 'Thread & Clothing Trend');
            $mail->addAddress($user_email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Exchange Request Status Update";
            $mail->Body = "<p>Dear User,</p>
                           <p>Your book exchange request (Request ID: $request_id) has been updated.</p>
                           <p>Status: <strong>$order_status</strong></p>
                           <p>Thank you,</p>
                           <p>Online Book Exchange</p>";

            $mail->send();
            header("Location: request_book_list.php?msg=Book status changed and email sent.");
            exit();
        } catch (Exception $e) {
            header("Location: request_book_list.php?error=Book status changed but email could not be sent.");
            exit();
        }
    } else {
        header("Location: request_book_list.php?error=Error updating status.");
        exit();
    }
}
