<?php

if (!isset($_GET['topic_id']) || !isset($_GET['comment'])) {
    exit('Missing parameters.');
}

$topic_id = (int) $_GET['topic_id'];
$comment = strip_tags($_GET['comment']);
$reply_to = isset($_GET['reply_to']) ? (int)$_GET['reply_to'] : null;

// Common email setup
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; // or your manual includes

function send_email($to, $name, $comment, $topic_id) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'louci786@gmail.com';
        $mail->Password   = 'twco uucx rqkn xyqq';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('louci786@gmail.com', 'Connect Converse');
        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Comment Notification";

        $body = "
            <p>Hi <strong>$name</strong>,</p>
            <p>You have a new comment:</p>
            <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$comment</blockquote>
            <p><a href='http://localhost/connect_converse/user/view_topics.php?topic_id=$topic_id'>View the discussion</a></p>
            <p style='color:gray'>This is an automated message. Please do not reply.</p>
        ";
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
    }
}

// 1. Notify Topic Author
$stmt = $conn->prepare("
    SELECT u.email, u.name 
    FROM users u 
    JOIN discussion_topics dt ON u.user_id = u.user_id 
    WHERE dt.topic_id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $row = $res->fetch_assoc()) {
    send_email($row['email'], $row['name'], $comment, $topic_id);
}

// 2. Notify Parent Comment Author if it's a reply
if ($reply_to) {
    $stmt2 = $conn->prepare("
        SELECT u.email, u.name 
        FROM users u 
        JOIN comments c ON u.user_id = c.user_id 
        WHERE c.comment_id = ?");
    $stmt2->bind_param("i", $reply_to);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    if ($res2 && $row2 = $res2->fetch_assoc()) {
        send_email($row2['email'], $row2['name'], $comment, $topic_id);
    }
}
