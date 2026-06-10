<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require '../db.php';

function notify_users($topic_id, $comment, $reply_to = null) {
    global $conn;

    $comment = strip_tags($comment);

    function send_email($to, $name, $comment, $topic_id) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'louci786@gmail.com';
            $mail->Password   = 'twco uucx rqkn xyqq'; // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('louci786@gmail.com', 'Connect Converse');
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject = "New Comment Notification";

            $mail->Body = "
                <p>Hi <strong>$name</strong>,</p>
                <p>You have a new comment:</p>
                <blockquote>$comment</blockquote>
                <p><a href='http://localhost/connect_converse/user/view_topics.php?topic_id=$topic_id'>View the discussion</a></p>
                <p style='color:gray'>This is an automated message. Please do not reply.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
        }
    }

    // Notify topic owner
    $stmt = $conn->prepare("
        SELECT u.email, u.name 
        FROM users u 
        JOIN discussion_topics dt ON dt.user_id = u.user_id 
        WHERE dt.topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        send_email($row['email'], $row['name'], $comment, $topic_id);
    }

    // Notify reply parent, if any
    if ($reply_to) {
        $stmt2 = $conn->prepare("
            SELECT u.email, u.name 
            FROM users u 
            JOIN comments c ON c.user_id = u.user_id 
            WHERE c.comment_id = ?");
        $stmt2->bind_param("i", $reply_to);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        if ($res2 && $row2 = $res2->fetch_assoc()) {
            send_email($row2['email'], $row2['name'], $comment, $topic_id);
        }
    }
}
