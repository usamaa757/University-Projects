<?php
include '../db.php';
include 'header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
// Fetch pending posts
$query = "SELECT discussion_topics.*, users.name FROM discussion_topics 
          JOIN users ON discussion_topics.user_id = users.user_id 
          WHERE discussion_topics.status = 'Pending'";
$result = $conn->query($query);

// Handle approval/rejection
if (isset($_POST['approve']) || isset($_POST['reject'])) {
    $topic_id = $_POST['topic_id'];
    $status = isset($_POST['approve']) ? 'approved' : 'rejected';

    // Fetch user_id for this topic
    $stmt = $conn->prepare("SELECT user_id, title FROM discussion_topics WHERE topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $title);
    $stmt->fetch();
    $stmt->close();

    // Update status
    $stmt = $conn->prepare("UPDATE discussion_topics SET status = ? WHERE topic_id = ?");
    $stmt->bind_param("si", $status, $topic_id);
    $stmt->execute();
    $stmt->close();

    // If approved, insert notification
    if ($status === 'approved') {
        $message = "Your post titled '$title' has been approved.";
        // Fetch user's email
        $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_email);
        $stmt->fetch();
        $stmt->close();

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'louci786@gmail.com';
            $mail->Password   = 'twco uucx rqkn xyqq';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('louci786@gmail.com', 'Discussion Forum');
            $mail->addAddress($user_email);
            $mail->addAddress($user_email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Post Approved';
            $mail->Body    = "<p>Hi there,</p><p>Your post titled <strong>'$title'</strong> has been approved and is now live on the platform.</p><p>Thanks,<br>Forum Team</p>";

            $mail->send();
            // echo 'Email has been sent';
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    }
    echo "<script type='text/javascript'>
    alert('Post has been " . ucfirst($status) . "!');
    window.location.href = 'manage_posts.php';
  </script>";
    exit();
}


?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Manage Discussion Posts</h3>

    <div class="table table-bordered table-hover">
        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php elseif ($row['status'] === 'approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="topic_id" value="<?= $row['topic_id'] ?>">
                                <button type="submit" name="approve" class="btn btn-sm btn-success">Approve</button>
                            </form>

                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="topic_id" value="<?= $row['topic_id'] ?>">
                                <button type="submit" name="reject" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>